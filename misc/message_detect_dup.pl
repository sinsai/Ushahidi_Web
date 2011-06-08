#! perl
# 重複messageを判定
# 標準入力かDatabaseからmessageを読み込み、
# 各messageのd日前以降のデータに同じmessageがあるか判定し、
# 標準出力かDatabaseに出力する。
# ただし、判定で切るのは現在から30日以内のデータとする。
#
# 重複判定方法：
#  レベル1：完全一致
#  レベル2:ハッシュタグと@ユーザ名と文字列「RT:」と文字列「QT:」をのぞいて完全一致
#  レベル3:ハッシュタグと@ユーザ名とurlと文字列「RT:」と文字列「QT:」を除いて完全一致
#  レベル4:ハッシュタグと＠ユーザ名とurlと文字列「RT:」と文字列「QT:」と特定キーワード（拡散希望、リマインドなど）を除いて完全一致
#
# 入力：Database か 標準入力 (-i で指定)
#    標準入力(stdin)の場合、「文\tID\t日付」を一行とするデータを読み込む（IDがない場合勝手につける、日付がない場合本日とする）
#		Database(db)の場合、messageテーブルから読み込む
# 出力：Database か 標準出力(-o で指定)
#     Database(db)の場合、message.typeに5を挿入。もしデータがあった場合は上書き。
#         標準出力(stdout)の場合、「文\tID\t日付\t重複先ID」を１行とするデータを読み込む
# 特定期間：n日前以降のmessageと比較する (-r で指定)。デフォルト0日、つまり、messageと同じ日。
# 判定レベル：上記重複判定の方法を選択(-l で指定)。デフォルト4。
# 新たに判定しなおす場合：キャッシュをクリアし再作成する(-n で指定)
#

use strict;
use utf8;
use Getopt::Std;
use FindBin;
use File::Path;

binmode STDIN,  ":utf8";
binmode STDOUT, ":utf8";
binmode STDERR, ":utf8";

my $g_DBHOST = "localhost";
my $g_DBNAME = "ushahidi";
my $g_DBUSER = "osm";
my $g_DBPASS = "osmosm";

my $g_CACHEDIR = File::Spec->catfile( $FindBin::Bin, 'cache' );
my $g_CASHERANGE = 30;

my $g_DEFAULT_RANGE = 0;
my $g_DEFAULT_LEVEL = 4;

&main;

sub main{
	my %opts = ();
	getopts ("i:o:r:l:nh", \%opts);
	if($opts{"h"}){
		&printUsage();
		return;
	}	
	$opts{"r"} = $g_DEFAULT_RANGE unless($opts{"r"});
	$opts{"l"} = $g_DEFAULT_LEVEL unless($opts{"l"});

	#Prepare cache
	if($opts{"n"}){
		rmtree($g_CACHEDIR) || die "Can not clear cashe-dir:$g_CACHEDIR";
	}
	if(! -d $g_CACHEDIR){
		mkpath($g_CACHEDIR) || die "Can not create cashe-dir:$g_CACHEDIR";
	}

	#Setup input obj
	my $in = undef;
	if($opts{"i"} eq "db"){
		$in = InputDb->new($g_DBHOST,$g_DBNAME,$g_DBUSER,$g_DBPASS);
	}elsif($opts{"i"} eq "stdin"){
		$in = InputStdin->new();
	}else{
		&printUsage();
	}
	unless($in){
		die "Can not open input resource\n";
	}

	#Setup output obj
	my $out = undef;
	if($opts{"o"} eq "db"){
		$out = OutputDb->new($g_DBHOST,$g_DBNAME,$g_DBUSER,$g_DBPASS);
	}elsif($opts{"o"} eq "stdout"){
		$out = OutputStdout->new();
	}else{
		&printUsage();
	}
	unless($out){
		die "Can not open output resource\n";
	}

	#open input obj
	$in->open();
	#open output obj
	$out->open();

	#Detection For each message
	while(my $message = $in->getNextMessage()){
		my $diff = &diffDays($message->{"date"});
		next if($diff > $g_CASHERANGE);
		my $dupMsgIds = &detectDuplicate($message, $opts{"r"}, $opts{"l"});
		$out->outputMessage($message, $dupMsgIds);
	}

	#close input obj
	$in->close();
	#close output obj	
	$out->close();

	return;
}

sub printUsage{
	print "-i (db|stdin)\tInput from Database or STDIN.\n";
	print "-o (db|stdin)\tOutput for Database or STDIN.\n";
	print "-r d\tRange of detection days.Default is 0.\n";
	print "-l (1|2|3|4)\tMethod of detection.Default is 4\n";
	print "-n\tRecreate cache data.\n";
	print "-h\tDisplay this message.\n";
	return;
}

sub diffDays{
	my $day1 = shift;
	my $day2 = shift;
	my $diff = 0;
	
	return undef unless($day1);
	unless($day2){
		my ($mday,$mon,$year) = (localtime(time))[3..5];
		$year += 1900;
		$mon += 1;
		$day2 = "$year-$mon-$mday"
	}
	$day1 =~ /^(\d\d\d\d)-(\d\d?)-(\d\d?)/;
	$day2 =~ /^(\d\d\d\d)-(\d\d?)-(\d\d?)/;
	#TODO:
	
	return $diff;
}

sub detectDuplicate{
	
}

package InputDatabase;

sub new{
	
}

sub open{
	
}

sub getNextMessage{
	
}

sub close{
	
}

package InputStdin;

sub new{
	my $class = shift;
	my $self = {};

	$self->{"lastid"} = 0;

	my ($mday,$mon,$year) = (localtime(time))[3..5];
	$year += 1900;
	$mon += 1;
	$self->{"today"} = "$year-$mon-$mday";

	return bless $self, $class;
}

sub open{
	#Nothing todo;
	return;	
}

sub getNextMessage{
	my $self = shift;
	
	my $message = {};
	my $dat = <STDIN>;
	return $dat unless($dat);

	$dat =~ s/[\r\n]+$//g;
	my @f = split(/\t/, $dat);
	$message->{'message'} = $f[0];
	$message->{'id'} = $f[1];
	$message->{'date'} = $f[2];

	#if id is nothing, set id.
	$message->{'id'} = $self->{'lastid'} unless($message->{'id'});
	$self->{'lastid'}++;
	
	#if date is nothing, set today.
	$message->{'date'} = $self->{'today'} unless($message->{'date'});

	return $message;
}

sub close{
	#Nothing todo;
}

package OutputDatabase;

sub new{
	
}

sub open{
	
}

sub outputMessage{
	
}

sub close{
	
}

package OutputStdout;

sub new{
	
}

sub open{
	
}

sub outputMessage{
	
}

sub close{
	
}
