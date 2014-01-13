#!/usr/bin/perl
use strict;
use warnings;

my @s = <"splits/txt/*.txt">;
$| = 1;
$\ = "\n";
open(my $out, '>', 'data.txt') or die "Unable to open file, $!";
#print scalar(@s);
my %d;
for (@s){ 
	open(my $f, '<', $_) or die "Unable to open file, $!";
	my $c = <$f> // "!";
	$d{$c}++;
	close $f;
	if($c =~ /[1-9]/){
		print $out "$c\t$_";
	}
}
close $out;
for (sort keys %d){
	print "$_:\t$d{$_}";
}