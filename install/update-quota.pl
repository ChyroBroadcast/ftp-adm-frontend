#! /usr/bin/perl

use strict;
use warnings;

use DBD::mysql;
use DBI;
use Getopt::Long;
use Net::SMTP;

##
# Source for using mysql from perl
# http://zetcode.com/db/mysqlperl/dbi/
# http://www.cyberciti.biz/faq/how-to-access-mysql-database-using-perl/
# http://www.perlmonks.org/?node_id=88222
##

my $do_alert_quota  = 0;
my $do_update_quota = 0;
my $log_file        = '/var/log/ftp-adm-frontend/update-quota.log';

my $host = 'localhost';
my $db   = 'qlowd';
my $user = 'qlowd';
my $pass = 'spider77';

GetOptions(
    'alert'        => \$do_alert_quota,
    'update_quota' => \$do_update_quota,
    'log_file=s'   => \$log_file,
);

if ( $do_alert_quota and $do_update_quota ) {
    print "Fatal error: you must choose one of these parameters in ('--alert', '--update_quota')\n";
    exit 1;
}

if ( !$do_alert_quota and !$do_update_quota ) {
    print "Fatal error: you must choose between '--alert' and '--update_quota' parameter\n";
    exit 1;
}

open( my $log_fd, '>>', $log_file )
    or die "Failed to open log file because $@";
select $log_fd;

my $dbh = DBI->connect( "DBI:mysql:$db:$host", $user, $pass );
unless ($dbh) {
	print "Failed to connect to database because $DBI::errstr";
	exit 2;
}

my $email = $dbh->prepare('SELECT fullname, email FROM User WHERE is_admin AND customer = (SELECT id FROM Customer WHERE path = ? LIMIT 1)');
unless ($email) {
	print "Failed to prepare query because $dbh->errstr";
	exit 2;
}

my $update = $dbh->prepare('UPDATE Customer SET used_space = ?, max_monthly_space = GREATEST(max_monthly_space, ?) WHERE path = ?');
unless ($update) {
	print "Failed to prepare query because $dbh->errstr";
	exit 2;
}

my @lines = qx!xfs_quota -x -c "df -N"!;
if ($?) {
	print "Failed to run 'xfs_quota' because $@";
	exit 2;
}

my $nb_updated = 0;
foreach (@lines) {
    chomp;

    if (/^(.+?)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%\s+(.+)$/) {
        my $used     = $3 * 1024;
		my $pct_used = $5;
        my $dir      = $6;

		if ( $do_alert_quota and $pct_used >= 80 ) {
			unless ( $email->execute($dir) ) {
				print "Failed to get admin users from customer ($dir) because $email->errstr\n";
				next;
			}

			unless ( $email->rows > 0 ) {
				print "No admin user found from customer ($dir)\n";
				next;
			}

			my $user = '';
			my @emails;

			while ( my $row = $email->fetchrow_arrayref ) {
				$user .= ', ' if length $user > 0;
				$user .= $row->[0];

				push @emails, '"' . $row->[0] . '" <' . $row->[1] . '>';
			}

			my $message = "Dear $user\n\nYour ftp account's space is runnin low (used: $pct_used%).\n\nBest regards\nQlowd";

			my $smtp = Net::SMTP->new('localhost');
			$smtp->mail('Qlowd (no-reply) <no-replay@qlowd.io>');
			$smtp->to(@emails, { SkipBad => 1 });
			$smtp->cc('Qlowd support <support@qlowd.io>');
			$smtp->data();
			$smtp->datasend('From: Qlowd (no-reply) <no-reply@qlowd.io>' . "\r\nTo: " . join(', ', @emails) . "\r\nCC: " . 'Qlowd support <support@qlowd.io>' . "\r\n$message\r\n");
			$smtp->dataend();
			$smtp->quit();
		}
		elsif ($do_update_quota) {
			if ( $update->execute( $used, $used, $dir ) ) {
				$nb_updated++;
			}
			else {
				print "Failed to update customer '$dir' with used_space='$used' because $update->errstr\n";
			}
		}
    }
}

print "Update-quota: $nb_updated directory updated\n";

$dbh->disconnect or warn $dbh->errstr;

