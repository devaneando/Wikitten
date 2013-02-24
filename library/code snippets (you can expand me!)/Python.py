# This is some code I copied from the bottle.py web framework (http://bottlepy.org)

import base64, cgi, email.utils, functools, hmac, imp, itertools, mimetypes,\
        os, re, subprocess, sys, tempfile, threading, time, urllib, warnings

from datetime import date as datedate, datetime, timedelta
from tempfile import TemporaryFile
from traceback import format_exc, print_exc

try: from json import dumps as json_dumps, loads as json_lds
except ImportError: # pragma: no cover
    try: from simplejson import dumps as json_dumps, loads as json_lds
    except ImportError:
        try: from django.utils.simplejson import dumps as json_dumps, loads as json_lds
        except ImportError:
            def json_dumps(data):
                raise ImportError("JSON support requires Python 2.6 or simplejson.")
            json_lds = json_dumps

# We now try to fix 2.5/2.6/3.1/3.2 incompatibilities.
# It ain't pretty but it works... Sorry for the mess.

py   = sys.version_info
py3k = py >= (3,0,0)
py25 = py <  (2,6,0)
py31 = (3,1,0) <= py < (3,2,0)

# Workaround for the missing "as" keyword in py3k.
def _e(): return sys.exc_info()[1]

# Workaround for the "print is a keyword/function" Python 2/3 dilemma
# and a fallback for mod_wsgi (resticts stdout/err attribute access)
try:
    _stdout, _stderr = sys.stdout.write, sys.stderr.write
except IOError:
    _stdout = lambda x: sys.stdout.write(x)
    _stderr = lambda x: sys.stderr.write(x)