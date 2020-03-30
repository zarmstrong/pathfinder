#!/usr/bin/env python
# encoding: utf8
import urllib2
from bs4 import BeautifulSoup
import json
import re
from datetime import datetime, date
import weasyprint
from weasyprint import HTML

revisiondata = ""
revisiondata=""

orgplaychangelog ="""<li>
0.08 – Large errata and clarification update
<ul>
<li>GM Basics – Clarified a number of rules.  Added Edicts and Anathema to the Table Variation section.</li>
<li>Glossary – Added "Assign a Chronicle" and "Apply a chronicle"
<li>Player Basics – Added RIP, clarified "Purchasing Guidelines"
<li>Organized Play Basics – updated Purchasing Guidelines, Chronicle sheet rules, and clarified who can make rulings for the campaign.</li>
<li>Character Options – Added link to additional options.</li></ul>
</li>
<li>0.07 – Clarified Boons a little more.  Moved Changelog to the bottom of the index page</li>
<li>0.06 – Fixed some typos and cut and paste errors</li> """

pdfchangelog ="""<li>updated to  Guild Guide  v0.08</li>  """

todolist = """ """

def getGuideVersion():
	page = urllib2.urlopen("http://www.organizedplayfoundation.org/encyclopedia/pfs2guide/")
	soup = BeautifulSoup(page, 'html.parser')   
	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	paras=pagecontent.find_all('p')
	for para in paras:
		if "current version:" in para.text.lower():
			verline= para.text.split("\n")
			vernum= (verline[0].split(":")[1]).strip()
			verdate = verline[1].replace("Current Version Date","").replace(u'\u2013',"@").strip()
			verstr = "Guide version " + str(vernum)+" (" + verdate +")"
			return str(vernum)+" (" + verdate +")"

def getpageheader(soup):
	header = soup.find('h1', attrs={'class':'entry-title'})
	return header.text.replace("Pathfinder Society (2nd edition)","").replace("Pathfinder Society (2nd edition) ","").replace(u"\u2018", "'").replace(u"\u2019", "'").replace("&rsquo;", "'").replace(u"\u2014", u"").replace(u"\u2013", u"").replace(u"\u2012", u"").replace(u"\u2011", u"").replace(u"\u2010", u"")

def changeHtags(soup):
	while True: 
		h5 = soup.find('h5')
		if not h5:
			break
		h5.name = 'h6'
	while True: 
		h4 = soup.find('h4')
		if not h4:
			break
		h4.name = 'h5'
	while True: 
		h3 = soup.find('h3')
		if not h3:
			break
		h3.name = 'h4'
	while True: 
		h2 = soup.find('h2')
		if not h2:
			break
		h2.name = 'h3'
	while True: 
		h1 = soup.find('h1')
		if not h1:
			break
		h1.name = 'h2'
	return soup

def replaceURLs(soup):
	debug = False
	urlList = [ 
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-quick-start-guide/register-new-character","#register"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-organized-play-basics/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-quick-start-guide/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pfs2ed-world-of-pfs/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pfs2edplayer-basics/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-player-basics/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-world-of-pfs/#pathfinder-society-training","#pathfinder-society-training"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-gm-basics/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-factions/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-faction-boons/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-legacy-backgrounds/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-character-options/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-glossary/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/volunteer-coordinators/#","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/trademarks-and-licenses/  #","#"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-organized-play-basics/","#opb"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-quick-start-guide/","#qsg"],
		["http://www.organizedplayfoundation.org/encyclopedia/pfs2ed-world-of-pfs/","#wopf"],
		["http://www.organizedplayfoundation.org/encyclopedia/pfs2edplayer-basics/","#playerbasics"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-gm-basics/","#gmbasics"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-factions/","#factions"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-faction-boons/","#factionboons"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-legacy-backgrounds/","#legback"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-character-options/","#chopt"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-glossary/","#gloss"],
		["http://www.organizedplayfoundation.org/encyclopedia/volunteer-coordinators/","#vol"],
		["http://www.organizedplayfoundation.org/encyclopedia/trademarks-and-licenses/","#tms"],
		["#negative-effects","#negative effects"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-world-of-pfs/","#wopf"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-player-basics/","#playbasic"],
		["table-4-total-value","#table-4-total-value"],
		["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-chronicle-sheets-and-record-keeping/","#chronicle-sheets-and-record-keeping"] ]
		
	for a in soup.findAll('a'):
		for urlpair in urlList:
			before = a['href']
			a['href'] = a['href'].replace(urlpair[0], urlpair[1])
			after = a['href']
			if debug == True:
				if before != after and 1 == 0:
					print urlpair[0]
					print before
					print after
					print "---"
				missedurls = []
				dedupedlist=[]
				if before == after:
					missedurls.append(before)
				for i in missedurls:
					if i not in dedupedlist:
						dedupedlist.append(i)
				print dedupedlist
		
	return soup

def removeCrapFromTags(soup):
	for tag in soup():
		for attribute in ["rel", "target"]: # You can also add id,style,etc in the list
			del tag[attribute]
	return soup

def fixTableStyles(soup):
	printout=u""
	for table in soup.find_all('table'):
		table['class']="table table-sm table-striped"
		rows = table.findAll('tr')
		for row in rows :
			for td in row.find_all('td', style=True):
				print ""
				#print td['style']


				#style = parseStyle(td['style'])
				#style['text-align'] = 'center'
				#td['style'] = style.cssText

	return soup

def removeChangelogAndBackTo(soup):
	#output=output.replace('<p><small>Back to: <span class="c-message__body" data-qa="message-text" dir="auto"></span></small></p>','')
	#output=output.replace('<p><small>Back to: </small></p>','')
	smalls = soup.findAll("small")
	for small in smalls:
		if "back to:" in small.text.lower():
			small.decompose()
		if "changelog" in small.text.lower():
			small.decompose()	
	ps = soup.findAll("p")
	for p in ps:
		if "current version:" in p.text.lower():
			p.decompose()			
		if "changelog" in p.text.lower():
			p.decompose()				
	return soup

def cleanUpPage(soup):
	replaceURLs(soup)
	changeHtags(soup)
	removeCrapFromTags(soup)
	fixTableStyles(soup)
	removeChangelogAndBackTo(soup)
	return soup

def getPages(pagelist):
	printout=u""
	for item in pagelist:
		print "printing " + str(item[1])
		pageurl = item[0]
		pagename=item[1]
		if item[1] == "qsg":
			divval='<div style="page-break-after: always;   page-break-inside: avoid;"></div>'
		else:
			divval='<div style=" -webkit-column-break-after: always; -webkit-column-break-inside: avoid; "></div>'
		divval='<div style="page-break-after: always;   page-break-inside: avoid;"></div>'
		page = urllib2.urlopen(pageurl)
		soup = BeautifulSoup(page, 'html.parser')
		headertitle=getpageheader(soup)
		if item[1] == "chopt" or item[1] == "gloss":
			printout=printout+ """
		""" + divval + """
			<a name="""+ pagename + """><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
			"""
		else:
			printout=printout+ """
		""" + divval + """
		<article>
			<a name="""+ pagename + """><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
			"""
		cleanUpPage(soup)

		pagecontent = soup.find('div', attrs={'class':'entry-content'})
		# pagecontent.findChildren()[0].decompose()
		# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
		# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
		# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
		navcontent = pagecontent.find('nav', attrs={'class':'navigation post-navigation'})
		if navcontent:
			navcontent.decompose()
		
		pagebreak_tag = soup.new_tag('div', style="page-break-after: always;   -webkit-column-break-inside: avoid; page-break-inside: avoid;")
		br_tag = soup.new_tag('br')
		pagecolbreak_tag = soup.new_tag('div', style="page-break-after: always;   -webkit-column-break-before: always;")
		div = pagecontent.find(id="pathfinder-training-table-all-schools-items")
		#if div is not None:
		#	div['style'] = "page-break-before: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"
		div = pagecontent.find(id="pathfinder-training-table-spells-items")
		if div is not None:
			div['style'] = " -webkit-column-break-before: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"			

		#div = pagecontent.find(id="pathfinder-training-table-swords-items")
		#if div is not None:
		#	div['style'] = "-webkit-column-break-inside: avoid; page-break-inside: avoid;"
		
		div = pagecontent.find("li", text="Pathfinder Training")
		wrapper = soup.new_tag('h3')
		if div is not None:
			wrapper.string = "Pathfinder Training"
			div.string=""
			div.append( wrapper)

		div = pagecontent.find(id="challenge-points")
		if div is not None:
			div['style'] = " -webkit-column-break-inside: avoid; page-break-inside: avoid;"

		div = pagecontent.find(id="table-3-bundle-value")
		if div is not None:
			div['style'] = "page-break-before: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"
		
		h2 = pagecontent.find("h2",text="Reviewing Chronicle Sheets")
		if h2 is not None:
			h2['id']='reviewing-chronicle-sheets'
		
		h4 = pagecontent.find("h4",text="Minor Factions")
		if h4 is not None:
			h4['style']="margin-top: 10px; "
			for i in range(7):
				br_tag = soup.new_tag('br')
				br_tag['name']=i
				h4.insert_before(br_tag)
			#h4.insert_before(pagecolbreak_tag)

		h4 = pagecontent.find(id="table-4-total-value")
		if h4 is not None:
			table = h4.find_next("table")
			treasuretable1=""" <table class="table table-sm table-striped" width=""><tbody><tr><td width="">Level</td><td width=""><strong>1 </strong><p><strong>Bundle </strong></p></td><td width=""><strong>2 Bundles</strong></td><td width=""><strong>3 Bundles</strong></td><td width=""><strong>4 Bundles</strong></td><td width=""><strong>5 Bundles</strong></td></tr><tr><td width=""><strong>1</strong></td><td width="">1.4</td><td width="">2.8</td><td width="">4.2</td><td width="">5.6</td><td width="">7</td></tr><tr><td width=""><strong>2</strong></td><td width="">2.2</td><td width="">4.4</td><td width="">6.6</td><td width="">8.8</td><td width="">11</td></tr><tr><td width=""><strong>3</strong></td><td width="">3.8</td><td width="">7.6</td><td width="">11.4</td><td width="">15.2</td><td width="">19</td></tr><tr><td width=""><strong>4</strong></td><td width="">6.4</td><td width="">12.8</td><td width="">19.2</td><td width="">25.6</td><td width="">32</td></tr><tr><td width=""><strong>5</strong></td><td width="">10</td><td width="">20</td><td width="">30</td><td width="">40</td><td width="">50</td></tr><tr><td width=""><strong>6</strong></td><td width="">15</td><td width="">30</td><td width="">45</td><td width="">60</td><td width="">75</td></tr><tr><td width=""><strong>7</strong></td><td width="">22</td><td width="">44</td><td width="">66</td><td width="">88</td><td width="">110</td></tr><tr><td width=""><strong>8</strong></td><td width="">30</td><td width="">60</td><td width="">90</td><td width="">120</td><td width="">150</td></tr><tr><td width=""><strong>9</strong></td><td width="">44</td><td width="">88</td><td width="">132</td><td width="">176</td><td width="">220</td></tr><tr><td width=""><strong>10</strong></td><td width="">60</td><td width="">120</td><td width="">180</td><td width="">240</td><td width="">300</td></tr></tbody></table>"""
			treasuresoup1 = BeautifulSoup(treasuretable1, "html.parser")
			treasuretable2="""<table class="table table-sm table-striped" width=""><tbody><tr><td width="">Level</td><td width=""><strong>6 Bundles</strong></td><td width=""><strong>7 Bundles</strong></td><td width=""><strong>8 Bundles</strong></td><td width=""><strong>9 Bundles</strong></td><td width=""><strong>10 Bundles</strong></td></tr><tr><td width=""><strong>1</strong></td><td width="">8.4</td><td width="">9.8</td><td width="">11.2</td><td width="">12.6</td><td width="">14</td></tr><tr><td width=""><strong>2</strong></td><td width="">13.2</td><td width="">15.4</td><td width="">17.6</td><td width="">19.8</td><td width="">22</td></tr><tr><td width=""><strong>3</strong></td><td width="">22.8</td><td width="">26.6</td><td width="">30.4</td><td width="">34.2</td><td width="">38</td></tr><tr><td width=""><strong>4</strong></td><td width="">38.4</td><td width="">44.8</td><td width="">51.2</td><td width="">57.6</td><td width="">64</td></tr><tr><td width=""><strong>5</strong></td><td width="">60</td><td width="">70</td><td width="">80</td><td width="">90</td><td width="">100</td></tr><tr><td width=""><strong>6</strong></td><td width="">90</td><td width="">105</td><td width="">120</td><td width="">135</td><td width="">150</td></tr><tr><td width=""><strong>7</strong></td><td width="">132</td><td width="">154</td><td width="">176</td><td width="">198</td><td width="">220</td></tr><tr><td width=""><strong>8</strong></td><td width="">180</td><td width="">210</td><td width="">240</td><td width="">270</td><td width="">300</td></tr><tr><td width=""><strong>9</strong></td><td width="">264</td><td width="">308</td><td width="">352</td><td width="">396</td><td width="">440</td></tr><tr><td width=""><strong>10</strong></td><td width="">360</td><td width="">420</td><td width="">480</td><td width="">540</td><td width="">600</td></tr></tbody></table>"""
			treasuresoup2 = BeautifulSoup(treasuretable2, "html.parser")
			h4.insert_after(treasuresoup2)
			h4.insert_after(treasuresoup1)
			table.decompose()


		tables = soup.findAll("table",width=True)
		if tables is not None:
			for table in tables:
				table['width']=""
		tds = soup.findAll("td",width=True)
		if tds is not None:
			for td in tds:
				td['width']=""				

		printout=printout+  ''.join(map(str, pagecontent.contents)).decode("utf8")
		#if item[1] == "qsg":
		#	printout=printout+'<div style="-webkit-column-break-after: before;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>'
		if item[1] == "playbasic": 
			printout=printout+'<div style="-webkit-column-break-after: before;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>'
		#if item[1] == "opb":
		# 	printout=printout+'<div style="-webkit-column-break-after: before;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>'
		if item[1] != "chopt" and item[1] != "gloss":
			printout=printout+"</article>"
	return printout

def quickstartguide():
	printout=u""
	page = urllib2.urlopen("http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-quick-start-guide/")
	soup = BeautifulSoup(page, 'html.parser')
	headertitle=getpageheader(soup)
	printout=printout+  """
	<!-- div style="page-break-after: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"></div -->
		<article><a name="qsg"><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
		"""
	cleanUpPage(soup)

	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	
	pagecontent.findChildren()[0].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	printout=printout+  ''.join(map(str, pagecontent.contents))
	return printout+"</article>"
	
def worldofpfs():
	page = urllib2.urlopen("http://www.organizedplayfoundation.org/encyclopedia/pfs2ed-world-of-pfs/")
	soup = BeautifulSoup(page, 'html.parser')
	headertitle=getpageheader(soup)
	printout=u""
	printout=printout+  """
	<div style="page-break-after: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"></div>
		<a name="wopf"><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
		<div class="wopf">  """
	cleanUpPage(soup)
	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	pagecontent.findChildren()[0].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()

	omenstart=0
	omenend=0
	for childofpage in pagecontent.findChildren( recursive=False):
		#print type(childofpage)
		#print childofpage.string
		#print "---"
		if "The Age Of Lost Omens" in childofpage:
			omenstart=1
			childofpage.decompose()
		else:
			if omenstart == 1 and omenend == 0:
				if "What Is The Pathfinder Society?" in childofpage:
					printout=printout+  unicode(childofpage)
					omenend = 1
				else:
					childofpage.decompose()
			else:
				printout=printout+unicode(childofpage)

	return printout+"</div>"
	#print ''.join(map(str, pagecontent.contents))

def vcs(page):
	page = urllib2.urlopen(page)
	pagedecode=page.read().decode()
	pagedecode=pagedecode.replace(u"\u00A0", " ").replace(u"\xc2\xa0", " ").replace(u"\u2018", "'").replace(u"\u2019", "'").replace("&rsquo;", "'").replace("&nbsp;", " ").replace(u"\u2014", u"-").replace(u"\u2013", u"-")
	printout=u""
	soup = BeautifulSoup(pagedecode, 'html.parser')
	headertitle=getpageheader(soup)
	printout=printout+  """
	<div style="page-break-after: always;   -webkit-column-break-inside: avoid; page-break-inside: avoid;"></div>
		<a name="vol"><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
		"""
	cleanUpPage(soup)

	opr = soup.find(text='PAIZO ORGANIZED PLAY REGIONS')
	opr.parent.parent.string="PAIZO ORGANIZED PLAY REGIONS"
	opr = soup.find("h2",string="PAIZO ORGANIZED PLAY REGIONS")
	opr['id']='opr'
	for ptag in opr.find_all_next("p"):
		ptag['class']="opr"	

	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	pagecontent.findChildren()[0].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	#pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()

	output= ''.join(map(str, pagecontent.contents)).replace("> ",">")
	#print type(output)
	printout=printout+  output
	#output=output
	return printout+""



def playerbasics():
	page = urllib2.urlopen("http://www.organizedplayfoundation.org/encyclopedia/pfs2edplayer-basics/")
	soup = BeautifulSoup(page, 'html.parser')
	headertitle=getpageheader(soup)
	printout=u""
	printout=printout+  """
	<div style="page-break-after: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"></div>
		<article><a name="playbasic"><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
		"""
	cleanUpPage(soup)
	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	pagecontent.findChildren()[0].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	
	div = soup.find(id="pathfinder-training-table-all-schools-items")
	new_tag = soup.new_tag('div', style="page-break-after: always;    -webkit-column-break-inside: avoid; page-break-inside: avoid;")
	div.insert_before(new_tag)

	div = soup.find(id="pathfinder-training-table-spells-items")
	div.insert_before(new_tag)
	printout=printout+  ''.join(map(str, pagecontent.contents))
	printout=printout+'<div style="-webkit-column-break-after: always;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>'
	return printout+"</article>"

def factionBoons():
	url="http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-faction-boons/"
	page = urllib2.urlopen(url)
	pagedecode=page.read().decode()
	pagedecode=pagedecode.replace(u"\u2018", "'").replace(u"\u2019", "'").replace("&rsquo;", "'").replace(u"\u2014", u"-").replace(u"\u2013", u"-")

	printout=u""
	soup = BeautifulSoup(pagedecode, 'html.parser')
	headertitle=getpageheader(soup)
	printout=printout+  """
	<div style="page-break-after: always;   -webkit-column-break-inside: avoid; page-break-inside: avoid;"></div>
		<article><a name="factionboons"><h1>""" + headertitle +"""</h1></a>&nbsp;&nbsp;<a href="#toc">^ back to top</a>
		"""
	cleanUpPage(soup)

	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	pagecontent.findChildren()[0].decompose()
	#pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	# pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	printout=printout+  ''.join(map(str, pagecontent.contents)).decode("utf8")
	return printout+"</article>"

	startpoint = soup.find(id="table-all-faction-boon")
	table = startpoint.find_next()
	#print table
	rows = table.findAll('tr')
	rownum=0
	boons=[]
	boons.append(["Professional Hireling","professional-hireling"])
	boons.append(["Expert Hireling","expert-hireling"])
	boons.append(["Vigilant Seal Champion, Improved","vigilant-seal-champion-improved"])
	boons.append(["Radiant Oath Champion, Improved","radiant-oath-champion-improved"])
	boons.append(["Horizon Hunters Champion, Improved","horizon-hunters-champion-improved"])
	boons.append(["Grand Archive Champion, Improved","grand-archive-champion-improved"])
	boons.append(["Vigilant Seal Champion, Improved","vigilant-seal-champion-improved"])
	boons.append(["Envoys' Alliance Champion","envoys-alliance-champion"])
	boons.append(["Envoys' Alliance Champion, Improved","envoys-alliance-champion-improved"])
	boons.append(["Verdant Wheel Champion, Improved","verdant-wheel-champion-improved"])
	boons.append(["Heroic Recall [Free Action]","heroic-recall-free-action"])
	boons.append(["Leader by Example","leader-by-example"])
	#boons.append(["Downtime","downtime"])

	untarn = soup.find(id="untarnished reputation")
	new_tag = soup.new_tag('p', id="untarnished-reputation" )
	new_tag['class']="boontitle"
	new_tag.string="Untarnished Reputation"
	untarn.insert_before(new_tag)
	untarn.decompose()
	
	for row in rows :
		colnum=0
		for td in row.find_all('td'):
			if rownum > 0:
				if colnum == 0:
					td_text_normal = td.text
					td_text = td.text.replace(' ', '-').lower()
					wrapper = soup.new_tag('a', href="#"+td_text)
					wrapper.string = td_text_normal
					td.string=""
					td.append( wrapper)
					boons.append([td_text_normal,td_text])
					#print td

				colnum = colnum+1
			#style = parseStyle(td['style'])
			#style['text-align'] = 'center'
			#td['style'] = style.cssText
		rownum=rownum+1
	startpoint = soup.find_all('h4')
	
	for h4 in startpoint:
		if "Table 3: Envoy" in h4.string or "Table 4: Grand Archive Boons" in h4.string or "Table 5: Horizon Hunters Boons" in h4.string or "Table 6: Radiant Oath Boons" in h4.string or "Table 7: Verdant Wheel Boons" in h4.string or "Table 8: Vigilant Seal Boons" in h4.string :
			
			table = h4.find_next()
			if table.name != "table":
				table = table.find_next()
			table['id'] = unicode(h4.string.encode("utf-8").replace(' ', '-').replace('_', '').lower(), 'ascii', 'ignore')
			#print table
			rows = table.findAll('tr')
			rownum=0
			for row in rows :
				colnum=0
				for td in row.find_all('td'):
					if rownum > 0:
						if colnum == 0:
							td_text_normal = td.text
							td_text = td.text.replace(' ', '-').lower()
							wrapper = soup.new_tag('a', href="#"+td_text)
							wrapper.string = td_text_normal
							td.string=""
							td.append( wrapper)
							boons.append([td_text_normal,td_text])
							#print td

						colnum = colnum+1
					#style = parseStyle(td['style'])
					#style['text-align'] = 'center'
					#td['style'] = style.cssText
				rownum=rownum+1

	startpoint = soup.find_all('h3')
	for h3 in startpoint:
		if "Boon List" in h3:
			h3['id']="boon-list"

	hireling = soup.find(style="color: mediumorchid;")
	hireling.parent.string="Hireling"
	hireling = soup.find("p", string="Hireling")
	hireling['id']='hireling'
	html = '<p><strong>Cost</strong> 4 Fame</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)	
	html = '<p><strong>Prerequisites</strong> All Factions Tier 0</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)
	html = '<p>ally</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)

	hireling = soup.find_all(text='Expert Hireling')
	hireling[1].parent.parent.string="Expert Hireling"


	hireling = soup.find("p", string="Expert Hireling")
	hireling['id']='expert-hireling'
	html = '<p><strong>Cost</strong> 6 Fame</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)	
	html = '<p><strong>Prerequisites</strong> All Factions Tier 2</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)
	html = '<p>slotless</p>'
	snippet = BeautifulSoup(html, 'html.parser').p.extract()
	hireling.insert_after(snippet)


	boonlist_start = soup.find(id="boon-list")
	for ptag in boonlist_start.find_all_next("p"):
		ptag['class']="boondetails"
	
	for tag in boonlist_start.find_all_next("p"):
		if "Capstone Boons" in tag.get_text():
			tag.string="Capstone Boons"
			tag['class']="capboontitle"
		for boon in boons:
			#print tag
			#print "boon '"+boon[0].encode('utf8', 'replace')+"'"
			if boon[0] == tag.get_text():
				tag['class']="boontitle"
				#print "FOUND: " + boon[0]
				#print tag.string 
				wrapper = soup.new_tag('a', id=boon[1])
				wrapper.string = boon[0]
				swrapper = soup.new_tag('strong')
				swrapper.append(wrapper)
				tag.string=""
				tag.append( swrapper)


	#return
	pagecontent = soup.find('div', attrs={'class':'entry-content'})
	pagecontent.findChildren()[0].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()
	pagecontent.findChildren()[len(pagecontent.findChildren())-1].decompose()

	h3 = pagecontent.find("h3",text="Grand Archive (Major)")
	if h3 is not None:
		h3['style'] = "page-break-before: always;  -webkit-column-break-inside: avoid; page-break-inside: avoid;"



	printout=printout+  ''.join(map(str, pagecontent.contents)).decode("utf8")
	return printout+"</article>"





def printPageHeader():
	printout= """<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content=
  "width=device-width, initial-scale=1" />
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

  <title>PF2 RPG Guild Guide</title><!-- Bootstrap -->
  <!-- Latest compiled and minified CSS -->
  <!-- link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" -->
  <link rel="stylesheet" href=
  "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
  integrity=
  "sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
  crossorigin="anonymous" type="text/css" />
  <!-- Custom styles for this template -->

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
  integrity=
  "sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
  crossorigin="anonymous" type="text/javascript">
</script>
  <script src=
  "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
  integrity=
  "sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
  crossorigin="anonymous" type="text/javascript">
</script>
  <script src=
  "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
  integrity=
  "sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
  crossorigin="anonymous" type="text/javascript">
</script>
  <!-- link href="style.css" rel="stylesheet" type="text/css" /-->
  <style>

 	@media print{ @page { margin: 0.05cm } }
	span.forcepagebreak { 
	   page-break-after: always; 
	   -webkit-column-break-after: always; -webkit-column-break-inside: avoid; page-break-inside: avoid;
	}
	table{
		-webkit-column-break-inside: avoid; page-break-inside: avoid;
	}
	h1, h2, h3, h4, h5{
		 page-break-after: avoid; -webkit-column-break-after: avoid;
	}
	article h1,  article h2,  article h3,  article h4,  article h5  {
		 background-color: #BBD8DC;
	}	
	.wopf h1,  .wopf h2,  .wopf h3,  .wopf h4,  .wopf h5   {
		 background-color: #BBD8DC;
	}	
	p{
		line-height: 1.3rem;	
	}	
	p.boondetails {
		margin-bottom:0rem;
	}
	#boondetails p.title {
		font-size: 1.65rem;
		font-weight: bold;
		margin-top:1.25rem;
	}	
	p.boontitle {
		font-size: 1.65rem;
		font-weight: bold;
		margin-top:1.25rem;
		margin-bottom:0rem;

	}		
	p.capboontitle {
		font-size: 1.85rem;
		font-weight: bold;
		margin-top:1.25rem;
		margin-bottom:0rem;

	}			
  	p.ofb {
	    margin-bottom:0.9rem;
	    line-height: 1rem;
	  }		
	p.opr {
		line-height: 1rem;	
		margin-bottom:0.3rem;
	}
	.ogl p, .ogl li {
		line-height: 1rem;	
		margin-bottom:0.3rem;
	}
	.roster p {
		line-height: 1rem;	
		margin-bottom:0.3rem;
	}	
	.roster li {
		line-height: 1rem;	
		margin-bottom:0.3rem;	
	}
	.glossary p {
		line-height: 1.3rem;	
		margin-bottom:0.5rem;
	}
	#tableofcontents li, #tableofcontents ul, #tableofcontents ul li{ 
		list-style: square outside !important;
	}	
	#tableofcontents {
	    background: #f9f9f9 none repeat scroll 0 0;
	    border: 1px solid #aaa;
	    display: table;
	    font-size: 130%;
	    padding: 7px 20px 0px 20px;
	    width: auto;
	}
	.toc_title {
	    font-weight: 700;
	    text-align: center;
	}
	article p, article h1,  article h2,  article h3,  article h4,  article h5,  article div  {
		height:100%;
	}
	article {
		min-height: 100vh;
	  -webkit-column-count: 2;
	     -moz-column-count: 2;
	          column-count: 2;
	          column-fill: auto;
	          height:100%;
  -webkit-column-rule: 1px dotted #ddd;
     -moz-column-rule: 1px dotted #ddd;
          column-rule: 1px dotted #ddd;
  -webkit-column-gap: 2em;
     -moz-column-gap: 2em;
          column-gap: 2em;     
          position: relative; height: 100%      	          
	}	
  </style>
</head>

<body>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src=
  "https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"
  type="text/javascript">
</script><!-- Latest compiled and minified JavaScript -->
  <script src=
  "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
  integrity=
  "sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
  crossorigin="anonymous" type="text/javascript">
</script>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 " id="toprowcol">
<div class="text-center">
	"""

	guidever=getGuideVersion()
	today = date.today()
	printout=printout+  """
      	<p class="text-center"><h1>Pathfinder Society (2nd edition) Roleplaying Guild Guide</h1></p>
      	<p class="text-center"><h2>Based off <a href="http://www.organizedplayfoundation.org/encyclopedia/pfs2guide/">Web Version """ + guidever + """</a></h2></p>
      	<p class="text-center"><h3>PDF Version """ + today.strftime("%B %d, %Y") + revisiondata + """</h3></p>
      	<p class="text-center"><h4>Compiled by Zach Armstrong, VL Denver, Colorado</h4></p>
      	<p class="text-center"><h5>Be sure to <a href="http://www.pfsprep.com/e107_plugins/forum/forum_viewtopic.php?3138">check here</a> for the latest version.</h5></p>

</div>

<h3>A quick order of business...</h3>
	<p class="ofb">Since the Pathfinder Society (2nd edition) Roleplaying Guild Guide has moved to web only (so I hear), I've compiled all the data into a single PDF for your convenience. This was quite a bit of work and I still have quite a bit more to do.</p>

<p class="ofb">I will try to keep this document up to date as much as possible, but do keep in mind that the web guide is the only official source and always contains the correct information, while this PDF may not have the latest updates.</p>
<p class="ofb">That said, thanks for downloading and I hope this helps you :)</p>
<p class="ofb">--Zach</p>
<hr class="my-4" />
<div id="tableofcontents">
    	<a name="toc"><h1>Table of Contents</h1></a>
<ul>

<li><a href="#qsg"><span>Quick Start Guide</span></a></li>
<li><a href="#opb"><span class="entry-title-old">Organized Play Basics</span></a></li>
<li><a href="#wopf"><span class="entry-title-old">The World of Pathfinder</span></a></li>
<li><a href="#playbasic"><span class="entry-title-old">Player Basics</span></a></li>
<li><a href="#gmbasic"><span class="entry-title-old">GM Basics</span></a></li>
<li><a href="#factions"><span class="entry-title-old">Factions</span></a></li>
<li><a href="#factionboons"><span class="entry-title-old">Faction Boons</span></a></li>
<li><a href="#legback"><span class="entry-title-old">Legacy Backgrounds</span></a></li>
<li><a href="#chopt"><span class="entry-title-old">Character Options</span></a></li>
<li><a href="#gloss"><span class="entry-title-old">Glossary</span></a></li>
<li><a href="#vol"><span class="entry-title-old">Volunteer Coordinators</span></a></li>
<li><a href="#tm"><span class="entry-title-old">Trademarks and Licenses</span></a></li>
</ul>
</div>
<hr class="my-4" /></span><br/>
<p class="ofb"><strong>Pathfinder Society (2nd edition) Roleplaying Guild Guide</strong> Changes since previous version:
	<ul>""" + orgplaychangelog.decode('utf8') + """   
	</ul>
</p>
<p class="ofb"><strong>PDF</strong> Changes since previous version:
	<ul>""" 	+ pdfchangelog.decode('utf8') + 	"""
	</ul>
	</p>
<p class="ofb">To do list:<ul>""" + todolist.decode('utf8') + """</ul>	
</p>
<p>If you find any errors or have suggestions, feel free to contact me at <a href="mailto:pf2guildguidepdf@traffid.com">pf2guildguidepdf@traffid.com</a></p>
	"""	
	return printout

def dofooter():
	printout= """
				</article>
		      </div>
		    </div>
		  </div>
		</body>
	</html> """
	return printout

print "getting ver"
ver=getGuideVersion().split(' ')
print "printing header"
output=printPageHeader()

pagelist = [ ["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-quick-start-guide/","qsg"], ["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-organized-play-basics/","opb" ] ]
output=output+getPages(pagelist)
print "printing world of"
output=output+worldofpfs()


pagelist=[ ["http://www.organizedplayfoundation.org/encyclopedia/pfs2edplayer-basics/", "playbasic"] , [ "http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-gm-basics/", "gmbasic" ] , [ "http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-factions/", "factions"] ]
output=output+getPages(pagelist)

output=output+factionBoons()


pagelist=[ ["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-legacy-backgrounds/", "legback" ],  ["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-character-options/" , "chopt"], ["http://www.organizedplayfoundation.org/encyclopedia/pathfinder-2-0-glossary/", "gloss"]]
output=output+getPages(pagelist)

print "printing vcs"
page="http://www.organizedplayfoundation.org/encyclopedia/volunteer-coordinators/"
output=output+vcs(page)

pagelist=[ ["http://www.organizedplayfoundation.org/encyclopedia/trademarks-and-licenses/", "tm"] ]
output=output+ '<div class="roster">'
output=output+getPages(pagelist)
output=output+ '</div>'
print "printing footer"
output=output+dofooter()

print "saving file"
file1 = open("p"+ ver[0]+"-cols.html","w")
file1.write(output.replace(u'\xa0', ' ').encode('utf-8'))
file1.close()

dopdf = True
if dopdf == True:
	print "creating pdf"
	HTML("p"+ ver[0]+"-cols.html").write_pdf('PF2 RPG Guild Guide v'+ ver[0]+' - Columns.pdf')
