#!E:/Users/shyzo/AppData/Local/Programs/Python/Python39/python.exe
# -*- coding: utf-8 -*-

#-----------------------------------------------------------------------------#
# Importation des librairies

import cv2
import json
import numpy as np
import time
from pdf2image import convert_from_path

#-----------------------------------------------------------------------------#
# Définition des fonctions
   
def get_contour_precedence(contour, cols):
    tolerance_factor = 10
    origin = cv2.boundingRect(contour)
    return ((origin[1] // tolerance_factor) * tolerance_factor) * cols + origin[0]

def rgb2gray(rgb):
    r, g, b = rgb[:,:,0], rgb[:,:,1], rgb[:,:,2]
    gray = 0.2989 * r + 0.5870 * g + 0.1140 * b
    return gray
 
def isChecked(img,percent):
    hauteur=len(img)
    largeur=len(img[0])
    compteur=0
    seuil=percent*hauteur*largeur/100
    for h in range(hauteur):
        for l in range(largeur):
            if img[h,l] == 0:
                compteur +=1;
    #print("C:",compteur,"S:",seuil, "Taille_Image:",hauteur*largeur)
    if compteur >= seuil :
        return "present"
    else:
        return "absent"
        
def retirerBordure(img,percent):
    h=len(img)
    l=len(img[0])
    seuil_h=h*percent/100
    seuil_l=l*percent/100
    return img[int(seuil_h/2):int(h-seuil_h/2),int(seuil_l/2):int(l-seuil_l/2)]
    
  
#-----------------------------------------------------------------------------#
# Définition des variables

res="";
case=0
compteur=0
output='{"1":"present"}'
output=json.loads(output)
pages = convert_from_path('test_tab_2.pdf', poppler_path='E:/Program Files/Poppler/Library/bin')
for page in pages:
    page.save('out.jpg','JPEG')


#-----------------------------------------------------------------------------#

# Load image
image = cv2.imread('out.jpg')

# Resize de l'image /!\ A adapter peut être avec la vraie feuille de présence
scale_percent = 50
width = int(image.shape[1] * scale_percent / 100)
height = int(image.shape[0] * scale_percent / 100)
dsize = (width, height)
image = cv2.resize(image, dsize)

# Grayscale, Otsu's threshold
original = image.copy()
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)[1]

# Remove text characters with morph open and contour filtering
kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (3,3))
opening = cv2.morphologyEx(thresh, cv2.MORPH_OPEN, kernel, iterations=1)
cnts = cv2.findContours(opening, cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)
cnts = cnts[0] if len(cnts) == 2 else cnts[1]
for c in cnts:
    area = cv2.contourArea(c)
    if area < 500:
        cv2.drawContours(opening, [c], -1, (0,0,0), -1)
close = 255 - cv2.morphologyEx(opening, cv2.MORPH_CLOSE, kernel, iterations=1)
cnts = cv2.findContours(close, cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)
cnts = cnts[0] if len(cnts) == 2 else cnts[1]
# Sort contours from top to bottom and from left to right
cnts.sort(key=lambda x:get_contour_precedence(x, close.shape[1]))


for c in cnts:
    area = cv2.contourArea(c)
    if area < 25000:
        x,y,w,h = cv2.boundingRect(c)
        cv2.rectangle(image, (x, y), (x + w, y + h), (36,255,12), -1)
        ROI = original[y:y+h, x:x+w]
        # Visualization
        case=case+1
        if((case-6)%9==4 and case>10):
            compteur+=1
            cv2.imshow('ROI', ROI)
            gray = cv2.cvtColor(ROI, cv2.COLOR_BGR2GRAY)
            thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)[1]
            croped = retirerBordure(thresh,10)
            presence=isChecked(croped,1)
            temp_json=json.dumps({str(compteur):presence},separators=(',', ':'))
            temp_json=json.loads(temp_json)
            output.update(temp_json)
print(output)

#Code pour reconnaissance
#try:
#    from PIL import Image
#except ImportError:
#    import Image
#import pytesseract 
#pytesseract.pytesseract.tesseract_cmd = 'E:\\Program Files\\Tesseract-OCR\\tesseract.exe' 
#img=Image.fromarray(ROI);
#temp=pytesseract.image_to_string(img)
#res=res+temp
