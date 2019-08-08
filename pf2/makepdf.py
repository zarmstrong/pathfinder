#!/usr/bin/env python

import pdfkit
#pdfkit.from_file('p0.03.html', 'PF2 RPG Guild Guide v0.03.pdf')
import weasyprint
from weasyprint import HTML
HTML('p0.03.html').write_pdf('PF2 RPG Guild Guide v0.03.pdf')
