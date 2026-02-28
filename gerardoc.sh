#!/bin/bash

#necessário baixar e instalar o phpdoc.phar no site do projeto

phpdoc -d ./bmpago,./butils -t ./docs --title="BMPago - Beto Mercado Pago PHP Lib"
