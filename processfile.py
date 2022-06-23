from distutils.command.clean import clean
from fileinput import filename
import array
import numpy as np
import sys

#
# Objetivos: 
# 
# Detectar caracteres que no deberían estar en el archivo (UNICODE o de control)
# Generar un archivo ¨limpio¨
#

if (len(sys.argv)<3):
    raise Exception("Numero invalido de argumentos")

# 'D:\\www\\woo1\\wp-content\\uploads\\fciadinucci-data.csv'
ori = sys.argv[1]
dst = sys.argv[2]

# Expected chars
allowed  = [10, 13] + list(range(ord(' '), ord('|')+1))

excluded = []

clean_file = ''

with open(ori) as file:
    lines = file.readlines()
    
    for line in lines:
        len = line.__len__()
        
        for i in range(0, len-1):
            if (not ord(line[i]) in allowed):
                # print(ord(line[i]))
                excluded.append(ord(line[i]))
            else: 
                clean_file += line[i]
                
        clean_file += "\r"
                
excluded = np.unique(excluded)
print(excluded)
    
    
f = open(dst, "w")
f.write(clean_file)
f.close()