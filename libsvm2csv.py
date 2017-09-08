#!/usr/bin/env python

"""
convert libsvm file to csv'
libsvm2csv.py <input file> <output file> <X dimensionality>
"""

import sys
import csv
import argparse

header=[ "att_1","att_2","att_3","att_4","att_5","att_6","att_7","att_8","att_9","att_10","att_11","att_12","class"]

parser = argparse.ArgumentParser( description = "convert libsvm file to csv" )
parser.add_argument( "input_file", help = "path to csv input file" )
parser.add_argument( "output_file", help = "path to output file" )
parser.add_argument( "dimensionality", type = int, help = "dimensionality of feature set, not including the label." )

args = parser.parse_args()

d = args.dimensionality
assert ( d > 0 )

reader = csv.reader( open( args.input_file ), delimiter = " " )
writer = csv.writer( open( args.output_file, 'wb' ))

for line in reader:
	label = line.pop( 0 )
	if line[-1].strip() == '':
		line.pop( -1 )

	# print line

	line = map( lambda x: tuple( x.split( ":" )), line )
#	line = 
	#print line
	# ('1', '0.194035105364'), ('2', '0.186042408882'), ('3', '-0.148706067206'), ...

	new_line = [ label ] + [ 0 ] * d
	for i, v in line:
		i = int( i )
		if i <= d:
			new_line[i] = v
	temp=new_line[ : ]
	temp.append(new_line[0])
	del temp[0]
	writer.writerow(header)
	writer.writerow( temp )
