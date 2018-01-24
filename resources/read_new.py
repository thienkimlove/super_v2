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

def render_complete(d):
    # AFAICT Selenium offers no better way to wait for the document to be loaded,
    # if one is in ignorance of its contents.
    return d.execute_script("return document.readyState") == "complete"


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

    if render_complete(driver):
       print(driver.page_source)
       driver.close()
load('#URL#')