#!C:/Python3.6/python.exe
# -*- coding: utf-8 -*-


import json
import cv2
from pdf2image import convert_from_path

pages = convert_from_path("uploads/scanTest.pdf", poppler_path="C:/poopler/Release-21.01.0/poppler-21.01.0/Library/bin")
for page in pages:
    page.save('out.jpg','JPEG')


image = cv2.imread('out.jpg')
original = image.copy()
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)[1]
kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (3,3))
thresh = cv2.dilate(thresh,kernel,iterations = 1)
xt=1300
x=1150
cnt=0
y=0
bord=0
numcase=0
bordh=0
cnt=0

output='{"1":"true"}'
output=json.loads(output)

res=""
test=0
while cnt <len(thresh):
    oui=0
    mean=0
    a=0
    if thresh[cnt,xt]>200:
        
        test+=1
        
        if test==1:

            bordh=cnt
            cnt+=10
        if test==2:
            test=0
            cnt-=3
            h=cnt-bordh
            numcase+=1
            
            for i in range (h):
                if thresh[cnt-h+i, x]>200:
                    oui+=thresh[cnt-h+i, x]            
                mean+=1
                
                
            try:
                a=oui/mean
            except:
                pass

            if (a>150 ):

                temp_json=json.dumps({str(numcase):str("absent")},separators=(',', ':'))
                temp_json=json.loads(temp_json)
                output.update(temp_json)
            else:
                temp_json=json.dumps({str(numcase):str("present")},separators=(',', ':'))
                temp_json=json.loads(temp_json)
                output.update(temp_json)
        
    else:
        cnt+=1
        


print(output)
    
        
 
