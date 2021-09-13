<?php 

    $redirectURL = urldecode("https://paydash.co.uk/checkout/" . $url);
?>
                <style>
                body {
    margin: 0;            /* Reset default margin */
}
iframe {
    display: block;       /* iframes are inline by default */
    background: #000;
    border: none;         /* Reset default border */
    height: 94vh;        /* Viewport-relative units */
    width: 100vw;
}
#second-header {
    display:  none !important;
}
.logo {
    display:  none !important;
}
footer {
    display:  none;
}
.checkout-page .text-center a.logo {
    display:  none !important;
}

</style>

<script type="text/javascript">
var frm = frames['paydash'].document;
var otherhead = frm.getElementsByTagName("head")[0];
var link = frm.createElement("https://apigamble.com/images/svg.css");
link.setAttribute("rel", "stylesheet");
link.setAttribute("type", "text/css");
link.setAttribute("href", "style.css");
otherhead.appendChild(link);
</script>

 
                <div class="container">
                    <iframe id="paydash" src="<?php echo $redirectURL; ?>" style="min-height: 100%; border: none !important;" border="0"></iframe>
                </div>


