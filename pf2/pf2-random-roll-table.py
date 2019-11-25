#!/usr/bin/env python
# encoding: utf8
import random

numberarray = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]

random.shuffle(numberarray)
random.shuffle(numberarray)
random.shuffle(numberarray)
random.shuffle(numberarray)

col = 1
line = ""
for num in numberarray:
	line = line + str(num)
	if col == 10:
		print line
		line =""
		col=1
	else:
		line = line + ","
		col += 1
