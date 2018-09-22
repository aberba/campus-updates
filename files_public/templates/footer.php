<?php
global $Session, $Settings, $js;

?>
    <footer id="footer" class="clearfix">
          <section class="group">
               <ul>
	               <li><a href="//<?php echo $Settings->site_facebook_url(); ?>" target="_blank"><img src="img/icons/facebook.png" alt="Our facebook page"></a></li>
	               <li><a href="//<?php echo $Settings->site_twitter_url(); ?>" target="_blank"><img src="img/icons/twitter.png" alt="Our twiter timeline"></a></li>
                 <li><a href="//<?php echo $Settings->site_youtube_url(); ?>" target="_blank"><img src="img/icons/youtube.png" alt="Our youtube channel"></a></li>
                 <li><a href="/contact/">Contact</a></li>
               </ul>
          </section>

          <section class="group">
               <ul>
                   <li><a href="/about/">About</a></li>
                   <li><a href="/about/#terms-of-use">Terms Of Use</a></li>
                   <li><a href="/about/#privacy">Privacy</a></li>

                   <?php if($Session->is_moderator()) echo "<li><a href='/admin/cms_dashboard.php'>Admin Panel</a></li>"; ?>
               </ul>
          </section>

          <section class="group copyright">
               <ul>
                   <li>&copy; <?php echo date("Y", time()) ." ". $Settings->site_name(); ?></li>
               </ul>
          </section>
    </footer>
</article>

<!--   JS FILES -->
<script src="//cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js" type="text/javascript"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js' type='text/javascript'></script>
<!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">
   window.jQuery || document.write('<script src="\/js\/jquery-1.11.0.min.js"><\/script>');
   window.Modernizr || document.write('<script src="\/js\/modernizr.js"><\/script>');
</script>

<script type="text/javascript" src="/js/global.js"></script>
<script type="text/javascript" src="/js/<?php echo @$js; ?>"></script>

</body>
</html>