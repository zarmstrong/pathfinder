#!/usr/bin/env python
# encoding: utf8
import MySQLdb
import urllib2
from bs4 import BeautifulSoup
import argparse
from pprint import pprint
import re
from StringIO import StringIO
import gzip
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities

import pandas as pd
import os
# parser = argparse.ArgumentParser(description='Process some arguments.')
# parser.add_argument("-s","--setname", help="Product set name",required=True)
# parser.add_argument("-u","--seturl", help="Product set URL",required=True)
# parser.add_argument("-m","--setmax", help="Max minis in set",required=True)
# parser.add_argument("-a","--setabbreviation", help="Product set abbreviation",required=True)
# args = parser.parse_args()

# db = MySQLdb.connect(host="localhost",	# your host, usually localhost
# 					 user="scraper",		 # your username
# 					 passwd="akPv38Rna_jt",  # your password
# 					 db="MMM_scraper_data")		# name of the data base
# db.set_character_set('utf8')
# db.autocommit = True

# cur = db.cursor()

# #'https://www.trollandtoad.com/pathfinder-battles-deadly-foes-singles/10304'
# df_page = args.seturl 
# setname = args.setname
# setmaxminis = args.setmax
# setabbreviation = args.setabbreviation

#add the product set, or fetch it's ID if already added



chromeOptions = webdriver.ChromeOptions() 
chromeOptions.add_argument("--no-sandbox") 
chromeOptions.add_argument("--disable-setuid-sandbox") 
chromeOptions.add_argument("--remote-debugging-port=9222")  # this
chromeOptions.add_argument("--disable-dev-shm-using") 
chromeOptions.add_argument("--disable-extensions") 
chromeOptions.add_argument("--disable-gpu") 
chromeOptions.add_argument("start-maximized") 
chromeOptions.add_argument("disable-infobars") 
chromeOptions.add_argument("--headless") 

driver = webdriver.Chrome(chrome_options=chromeOptions) 
# driver.get("https://paizo.com/store/starfinder/society/season1")
# driver.get("https://paizo.com/store/starfinder/society/season2")
# driver.get("https://paizo.com/store/starfinder/society/season3")
driver.get("https://paizo.com/store/starfinder/society/quests")
driver.implicitly_wait(100)

#print driver.page_source
# page = urllib2.urlopen("https://paizo.com/store/pathfinder/society/season10")
# if page.info().get('Content-Encoding') == 'gzip':
#     buf = StringIO(page.read())
#     f = gzip.GzipFile(fileobj=buf)
#     data = f.read()
#     page=data

soup = BeautifulSoup(driver.page_source, 'html.parser')
#print soup
# results = soup.find('div', {"id": "tabs"})
# print results

pattern = re.compile(r'All Products')
if soup.find(text=pattern):
	allprod=soup.find(text=pattern).parent
	allprodurl="https://paizo.com"+ allprod.attrs['href']
	driver.get(allprodurl)
	driver.implicitly_wait(100)
	soup = BeautifulSoup(driver.page_source, 'html.parser')
#print soup
links=[]
results = soup.findAll('div', attrs={'class':'product'})
for product in results:
	link=product.find('a', href=True)
	links.append(link.attrs['href'])

for link in links:
	driver.get(link)
	driver.implicitly_wait(100)
	soup = BeautifulSoup(driver.page_source, 'html.parser')
	scenariotitle=soup.find('h1', attrs={'itemprop':'name'}).getText().replace(u"\u2014", u"-").replace(u"\u2013", u"-").replace(u"\u2012", u"-").replace(u"\u2011", u"-").replace(u"\u2010", u"-")
	scenariodesc=soup.find('div', attrs={'itemprop':'description'}).getText().replace(u"\u2014", u"-").replace(u"\u2013", u"-").replace(u"\u2012", u"-").replace(u"\u2011", u"-").replace(u"\u2010", u"-")
	#print scenariotitle
	#print scenariodesc
	if "#" in scenariotitle:
		scenariotitleinfo=scenariotitle.split("#",1)
	else:
		scenariotitleinfo=scenariotitle.split(":",1)
	#print scenariotitleinfo
	#if "#" in scenariotitleinfo
	game=scenariotitleinfo[0].strip()
	numbername=scenariotitleinfo[1].split(":",1)
	if "Quest" in scenariotitleinfo[0]:
		gamename=scenariotitleinfo[1].replace("'","\'")
		gameseason="9"
		scenarionum="Q"
		gamenum="q"
	else:
		try:
			gamenum=numbername[0].split("-",1)
			gamename=numbername[1].strip().replace(u"\u2018", "'").replace(u"\u2019", "'").replace("&rsquo;", "'").replace(u"\u2014", u"-").replace(u"\u2013", u"-").replace(u"\u2012", u"-").replace(u"\u2011", u"-").replace(u"\u2010", u"-").replace('"','\\"').replace("'","\\'")
			gameseason=gamenum[0].strip()
			scenarionum=gamenum[1].strip()
			
		except:
			gameseason="0"
			scenarionum=gamenum[0].strip()

	if "Pathfinder Society Scenario" in game:
		gametype="pfs"
	if "Pathfinder Society Quest" in game:
		gametype="pfs"		
	if "Starfinder Society Scenario" in game:
		gametype="sfs"
	levels="0-0"
	levellow="1"
	levelhigh="1"
	output=""
	for line in scenariodesc.replace(u"\u2018", "'").replace(u"\u2019", "'").replace("&rsquo;", "'").replace(u"\u2014", u"-").replace(u"\u2013", u"-").replace(u"\u2012", u"-").replace(u"\u2011", u"-").replace(u"\u2010", u"-").splitlines():
		if "designed for levels" in line:
			lineparts = line.split(" levels ",1)
			levels=lineparts[1].replace(" ", "").replace(".","").split("-",1)
			levellow=levels[0].strip()
			levelhigh=levels[1].strip()
		elif "Scenario Tag" in line:
			taglist=line.split(":")[1]
			taglist=taglist.strip()
			if taglist=="None":
				taglist=""
		elif "Written by" in line or "Release: " in line or "nario is designed for play in Path" in line:
			#do nothing
			output=output
		else:
			output+=line.strip()+"\n"
		#print "line: " + line

	#print "Game: "+game
	#print "gameseason: "+gameseason
	#print "scenarionum: "+scenarionum
	#print "gamename: "+gamename
	#print "levels: " +levels
	#print output
	lineout="INSERT IGNORE INTO scenarios (gametype,gameseason,scenarionum,scenarioname,scenariodesc,levellow,levelhigh,scenariotags) VALUES ('"+gametype+"','"+gameseason+"','"+scenarionum+"','"+gamename+"','"+output.replace('"','\\"').replace("'","\\'").rstrip("\n\r").rstrip("\n")+"','"+levellow+"','"+levelhigh+"','"+taglist+"');"
	print lineout.replace(u"\u2018", "\'").replace(u"\u2019", "\'").replace("&rsquo;", "\'").replace(u"\u2014", u"-").replace(u"\u2013", u"-").replace(u"\u2012", u"-").replace(u"\u2011", u"-").replace(u"\u2010", u"-").replace(u"\u201d", u'\"').replace(u"\u201c", u'\"').replace(" (PFRPG)", "").replace(" PDF","")
# results = soup.find('div', attrs={'class':'result-container'})

# products = results.findAll('div', attrs={'class':'product-col'})
# for product in products:
# 	productinfo=product.find('div', attrs={'class':'product-info'})
# 	productname=productinfo.find('div', attrs={'class':'prod-title'})
# 	productA=productname.find('a')
# 	productlink="https://www.trollandtoad.com" + productA.attrs['href']
# 	producttitle=productA.text

# 	#add the product, or fetch it's ID if already added
# 	cur.execute("SELECT pid FROM product_names WHERE product_name = %s", [ producttitle ] )
# 	rows = cur.fetchall()
# 	if cur.rowcount == 0:
# 		print "adding " + producttitle
# 		cur.execute("INSERT INTO product_names (product_name) VALUES (%s)", [ producttitle ] )
# 		item_id=cur.lastrowid
# 	else:
# 		for row in rows:
# 			item_id = row[0]

# 	#add the product/set relationship if it doesn't exist
# 	cur.execute("SELECT product_id FROM product_sets WHERE product_id = %s", [ item_id ] )
# 	rows = cur.fetchall()
# 	if cur.rowcount == 0:
# 		cur.execute("INSERT INTO product_sets (product_set_id, product_id) VALUES (%s,%s)", [ set_id,item_id ] )
	
# 	#add the URL if it doesn't exist
# 	#print "Checking rows: SELECT product_id FROM product_urls WHERE product_id = %s", [ item_id ]
# 	#print item_id
# 	cur.execute("SELECT product_id FROM product_urls WHERE product_id = %s", [ item_id ] )
# 	rows = cur.fetchall()
# 	if cur.rowcount == 0:
# 		print "adding URL for " + producttitle
# 		cur.execute("INSERT INTO product_urls (product_id, product_url_tt) VALUES (%s,%s)", [ item_id, productlink ] )

# db.commit()