<?php global $Settings, $js; ?>

	<footer id="footer">
        <p>&copy; <?php echo $Settings->site_name() ." ". date("Y", time()) ; ?></p>
	</footer>

</article>

   <!-- scripts -->
<script src="./js/jquery-1.11.0.min.js" type="text/javascript"></script>
    
<!-- HTML% Shiv -->
<!--[if lt IE 9]>
   <script src="http://html5shiv.googlecode.com/svn/trunk/html5-els.js"></script>
<![endif]-->

<!--   JS FILES -->
<script type="text/javascript" src="./js/cms_global.js"></script>
<script type="text/javascript" src="./js/<?php echo @$js; ?>"></script>

</body>
</html>