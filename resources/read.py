#!/usr/bin/python
# coding=utf8
# the above tag defines encoding for this document and is for Python 2.x compatibility

from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from selenium.common.exceptions import TimeoutException, WebDriverException
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
import re
import os

def readystate_complete(d):
    # AFAICT Selenium offers no better way to wait for the document to be loaded,
    # if one is in ignorance of its contents.
    return d.execute_script("return document.readyState") == "complete"

def itune_check(content):
    regex = r"[\"'](.*\:\/\/itunes.apple.com.*)[\"']"
    try:
        result = re.findall(regex, content, re.IGNORECASE)[0]
        result = result.replace("'", '')
    except Exception, IndexError:
        result = None
    return result

def refresh_meta_but_not_have_link(content):
    regex = r"meta\s*http-equiv\s*=[\"']?refresh[\"']?\s*content=[\"']?\d*\s*;?"
    try:
        result = re.findall(regex, content, re.IGNORECASE)[0]
        regex2 = r"rel=\"noreferrer\" href=\"(.*)\""
        result2 = re.findall(regex2, content, re.IGNORECASE)[0]
        result2 = result2.replace("'", '')
    except Exception, IndexError:
        result = None
        result2 = None

    if result is not None and result2 is not None:
        return result2
    else:
        return None

def new_redirect(content):
    regex = r"meta\s*http-equiv\s*=[\"']?refresh[\"']?\s*content=[\"']?\d*\s*;?\s*url=[\"\']?(https?:\/\/.*)['\"]"
    try:
        result = re.findall(regex, content, re.IGNORECASE)[0]
        result = result.replace("'", '')
    except Exception, IndexError:
        regex = r"document\.location\.href\s*=\s*[\"'](https?:\/\/.*)[\"']"
        try:
            result = re.findall(regex, content, re.IGNORECASE)[0]
            result = result.replace("'", '')
        except Exception, IndexError:
            result = None
    return result

def have_redirect(content):
    regex = r".location"
    try:
        result = re.findall(regex, content, re.IGNORECASE)[0]
        result = result.replace("'", '')
    except Exception, IndexError:
        result = None
    return result

def page_has_loaded(driver):
    page_state = driver.execute_script('return document.readyState;')
    return page_state == 'complete'

def load(url):
    dcap = dict(DesiredCapabilities.PHANTOMJS)
    dcap["phantomjs.page.settings.userAgent"] = (
        "#AGENT#"
    )
    dcap["javascriptEnabled"] = True
    service_args = [
                     '--proxy=162.243.173.214:22225',
                     '--proxy-auth=#USERNAME#:99oah6sz26i5',
                     '--proxy-type=sock5',
                     ]
    driver = webdriver.PhantomJS(executable_path=r'/usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs',desired_capabilities=dcap, service_args=service_args)

    driver.get(url)

    if readystate_complete(driver):
       source = driver.page_source
       redirect = new_redirect(source)
       if redirect is not None:
          driver.close()
          load(redirect)
       else:
           redirect2 = refresh_meta_but_not_have_link(source)
           if redirect2 is not None:
               driver.close()
               load(redirect2)
           else:
               with open("/tmp/#OFFERID#_last.html", "w") as text_file:
                  text_file.write(source.encode('utf8'))
               os.chmod("/tmp/#OFFERID#_last.html", 0777)
               driver.save_screenshot("/tmp/#OFFERID#_last.png")
               os.chmod("/tmp/#OFFERID#_last.png", 0777)
               itune = itune_check(source)
               if itune is not None:
                  print(itune)
               driver.close()


load('#URL#')