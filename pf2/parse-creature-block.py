#!/usr/bin/env python
# encoding: utf8
from bs4 import BeautifulSoup
from tidylib import tidy_document

fullstatblock=""
filepath = '/tmp/creature'
speedline=False
try:
    with open(filepath) as fp:
        line = fp.readline()
        cnt = 1
        while line:
           
            if cnt == 1:
                soup = BeautifulSoup(line, "html.parser")
                text = soup.get_text()
                fullstatblock+=  """      <div class="col-md-6 " id="">

                <div class="row">
                    <div class="col-md-6 text-capitalize font-weight-bold" id="">
                        <h5 class="mt-0 mb-0 ">""" + text + """</h5>
                    </div>"""
            elif cnt == 2:
                soup = BeautifulSoup(line, "html.parser")
                text = soup.get_text()
                fullstatblock+=  """
                                <div class="col-md-6 text-capitalize  font-weight-bold text-right" id="">
                        <h5 class="mt-0 mb-0 ">""" + text + """</h5>
                    </div>              
                </div>
                <hr class="mt-0 mb-0 " style="border: solid 2px black;" />"""
            elif cnt == 3:
                soup = BeautifulSoup(line, "html.parser")
                # for e in soup.findAll('br'):
                #     e.extract() 
                line3value="<span class='bookinfo'>" + str(soup) +"</span>"
            elif cnt == 4:
                soup = BeautifulSoup(line, "html.parser")
                for e in soup.findAll('br'):
                    e.extract() 
                for e in soup.findAll('b'):
                    tags = e.get_text()
                for tag in tags.split(','):
                    
                    if tag.strip() in ['LG', 'NG', 'CG', 'LN', 'N', 'CN', 'LE', 'NE', 'CE']:
                        fullstatblock+= """<span class="badge badge-info text-uppercase">""" + tag.strip() + """</span> """
                    elif tag.strip() in ['Tiny', 'Medium', 'Large', 'Huge', ]:
                        fullstatblock+= """<span class="badge badge-success text-uppercase">""" + tag.strip() + """</span> """
                    elif tag.strip() in ['Unique' ]:
                        fullstatblock+= """<span class="badge badge-warning text-uppercase">""" + tag.strip() + """</span> """
                    else:
                        fullstatblock+= """<span class="badge badge-danger text-uppercase">""" + tag.strip() + """</span> """
                fullstatblock+= "<br/>\n"
                fullstatblock+= line3value
            elif cnt == 5:
                fullstatblock+=""
            else:
                if "<b>Speed</b> " in line:
                    speedline=True
                
                if speedline == True:
                    soup = BeautifulSoup(line, "html.parser")
                    for e in soup.findAll('br'):
                        e.extract()                     
                    fullstatblock+='<div class="hang" >' + soup.get_text().strip() + '</div>\n'
                else:
                    fullstatblock+=line.strip() + "\n"
                #("Line {}: {}".format(cnt, line.strip()))  

            line = fp.readline()
            cnt += 1


finally:
    soup = BeautifulSoup(fullstatblock, "html.parser")
    for e in soup.findAll('hr'):
        e['class'] = [u'mt-0', u'mb-0']
    outtext=str(soup)
    outtext = outtext.replace('[1]', "&#9670;").replace("[2]", "&#9670;&#9670;").replace("[3]", "&#9670;&#9670;&#9670;").replace("[R]", "&#10558;").replace("[F]", "&#9671;")
    print outtext
    #print tidy_document(str(soup))

    fp.close()