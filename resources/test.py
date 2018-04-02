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
import codecs

def readystate_complete(d):
    # AFAICT Selenium offers no better way to wait for the document to be loaded,
    # if one is in ignorance of its contents.
    try:
        return d.execute_script("return document.readyState") == "complete"
    except:
        return False

def load(url):
    dcap = dict(DesiredCapabilities.PHANTOMJS)
    service_args = []
    dcap["javascriptEnabled"] = True
    driver = webdriver.PhantomJS(executable_path=r'/usr/local/share/phantomjs-2.1.1-linux-x86_64/bin/phantomjs',desired_capabilities=dcap, service_args=service_args)

    driver.get(url)
    resume = wait.until(EC.presence_of_element_located((By.XPATH,"//button[@type='submit' and @class='btn']")))
    driver.implicitly_wait(50)
    resume.click()   
    wait(driver, 120).until_not(EC.title_is(title))
    driver.save_screenshot("/var/www/html/super_v2/pubic/test/#OFFERID#_last.png")
    os.chmod("/tmp/#OFFERID#_last.png", 0o777)
    driver.close()
load('#URL#')