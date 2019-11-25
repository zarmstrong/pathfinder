#!/usr/bin/env python
# encoding: utf8
import urllib2
from bs4 import BeautifulSoup
import boto3

previousver = "0.07"
previousdate ="9/30/2019 @ 12:00"

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
			return [ vernum, verdate]

def emailNotify(pv,pd,cv):
	SENDER = "Zach <zach@zacharyarmstrong.com>"
	RECIPIENT = "zach@zacharyarmstrong.com"
	AWS_REGION = "us-east-1"
	CHARSET = "UTF-8"
	SUBJECT = "PFS Guide 2.0 Updated"	
	BODY_TEXT = """Previous version: """ + pv + """<br/>
	Previous Date: """ + pd + """<br/>
	Current version: """ + cv[0] + """<br/>
	Current Date:""" + cv[1] 

	client = boto3.client('ses',region_name=AWS_REGION)
	# Try to send the email.
	try:
	    #Provide the contents of the email.
	    response = client.send_email(
	        Destination={
	            'ToAddresses': [
	                RECIPIENT,
	            ],
	        },
	        Message={
	            'Body': {
	                'Html': {
	                    'Charset': CHARSET,
	                    'Data': BODY_TEXT,
	                },
	                'Text': {
	                    'Charset': CHARSET,
	                    'Data': BODY_TEXT,
	                },
	            },
	            'Subject': {
	                'Charset': CHARSET,
	                'Data': SUBJECT,
	            },
	        },
	        Source=SENDER
	    )
	# Display an error if something goes wrong.	
	except ClientError as e:
	    print(e.response['Error']['Message'])
	else:
	    print("Email sent! Message ID:"),
	    print(response['MessageId'])

def compareversion(pv, pd):
	curver=getGuideVersion()
	if pv != curver[0]:
		emailNotify(pv,pd,curver)
		print "Version number mismatch"
		return
	if pd != curver[1]:
		emailNotify(pv,pd,curver)
		print "Version date mismatch"
		return

	print "Looks the same"		
	return

if __name__ == "__main__":
    compareversion(previousver,previousdate)
