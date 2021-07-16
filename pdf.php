<html>
    <head>
        <title>WEHUB - Ficha de projeto</title>
    </head>
    <style>
        #viewpdf
        {
            height: 800px;
        }
    </style>
    <body>
        
        <div class="container">
            
            <div id="viewpdf">
                
                
            </div>
            
        </div>
        
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.5/pdfobject.js" integrity="sha512-eCQjXTTg9blbos6LwHpAHSEZode2HEduXmentxjV8+9pv3q1UwDU1bNu0qc2WpZZhltRMT9zgGl7EzuqnQY5yQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var viewpdf = $("#viewpdf");
        
        PDFObject.embed("WEHUB.pdf", viewpdf);
    </script>
</html>