#! /usr/bin/perl

use strict;
use warnings;

use DBD::mysql;
use DBI;

##
# Source for using mysql from perl
# http://zetcode.com/db/mysqlperl/dbi/
# http://www.cyberciti.biz/faq/how-to-access-mysql-database-using-perl/
##

my $log_file = '/var/log/ftp-adm-frontend/update-quota.log';
open( my $log_fd, '>>', $log_file )
    or die "Failed to open log file because $@";
select $log_fd;

my $host = 'localhost';
my $db   = 'qlowd';
my $user = 'qlowd';
my $pass = 'spider77';

my $dbh = DBI->connect( "DBI:mysql:$db:$host", $user, $pass )
    or die "Failed to connect to database because $DBI::errstr";
my $update = $dbh->prepare('UPDATE Customer SET used_space = ? WHERE path = ?')
    or die "Failed to prepare query bacuse $dbh->errstr";

my @lines = qx!xfs_quota -x -c "df -N"!;
die "Failed to run 'xfs_quota' because $@" if $?;

my $nb_updated = 0;
foreach (@lines) {
    chomp;

    if (/^(.+?)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+%)\s+(.+)$/) {
        my $used = $3 * 1024;
        my $dir  = $6;

        if ( $update->execute( $used, $dir ) ) {
            $nb_updated++;
        }
        else {
            print
                "Failed to update customer '$dir' with used_space='$used' because $update->errstr\n";
        }
    }
}

print "Update-quota: $nb_updated directory updated\n";

$dbh->disconnect or warn $dbh->errstr;

