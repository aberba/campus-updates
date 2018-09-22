<?php
require_once('../files_public/includes/initialize.php');

if (!$Settings->site_online()) {
	redirect_to("/offline/");
}

$css              = "about.css";
$js               = "about.js";
$page_description = "About";
$page_title       = "About";


include_template('header.php'); 
?>

<section id="content"> <!-- left content -->

        <section class="page-section">
	        <h1>About Campus Updates</h1>
			<p>Campus Updates is a community for sharing news, updates and happenings on campuses worldwide.</p>

			<p>Targeted campuses are high/tech schools, collages, polytechnics and universities. 
			Information on Campus Updates is not only entertaining, but educative. Members can 
			have full access to the community as well as share and post comments. The community 
			is not bias, and therefore allow members to express themselves fully without any constraint, fear or 
			favour.</p>

			<h3>Agenda</h3>
			<p>Campus Updates seeks to promote the sharing of news, updates, happenings, and all relevant 
			information that will keep our users abbreased, entertained and connected.</p>

			<h3>Our Information</h3>
			<p>Information, documents and graphics published on our site are from our users,  
			informants, anonymous persons, research, and many other reliable sources. We analyse 
			these information and conduct the neccessary investigations if posible before publishing them.</p>

			<p>We do not make public, personal information of our users and 
			informants (if any), unless stated otherwise by they themselves.</p>    

		    <p>Confidentiality of contributors is ensured and we guarantee the best 
			utitlization of their information. We also respect the terms of our informants 
			and we are open to any correction and views of our users.</p>

			<h3>Who are we?</h3>	
			<p>Campus Updates is a community and therefore any registered user becomes a part of the community. 
			The site has various cultures of recruiting members who are trusted and contribute tremendously to be part 
			the moderator team. For more information on the moderators please visit the <a href="/contact/#moderators">contact page</a>.</p>
        </section>

        <section id="privacy" class="page-section">
            <h1>Privacy</h1>
        	
        	<h3>What information do we collect?</h3>
            <p>We collect information from you during <a href="/signup/">registration</a>. You may be asked 
            to enter your name, e-mail address, gender and a username.</p>

            <h3>What do we use your information for?</h3>
            <p>Any of the information we collect from you may be used in one or more of the following ways: </p>

            <p>
	           <ul>
	              <li>To personalize your experience (your information helps us to better respond to your individual needs).</li>
	              <li>To improve our website (we continually strive to improve our website offerings based on the information and 
	                feedback we receive from you).</li>
	              <li>To keep track of your contributions. (ownership of your contribution and information is ensured based on the information you provide us).</li>
	           </ul>
            </p>

             <h3>Do we use cookies?</h3>
             <p>Yes.</p> 
             <p>Cookies are small files that a site or its service provider transfers to your computer
             storage through your web browser (if it is enabled in your browser). 
             <p>Cookie storage enables this sites or service providers' systems to recognize your browser and remember certain information all to enhance your expirience.</p>

             <h3>Do we disclose any information to outside parties?</h3>
             <p>We do not sell, trade, or otherwise transfer to outside parties your personal information.</p>

             <p>This does not include trusted third parties who assist us in operating our website, conducting our business, 
             or servicing to you, as long  those parties agree to keep this information confidential. We may also release your 
             information when we believe release is appropriate to comply with the law, enforce our site policies, or protect 
             ours or others rights, property, or safety.</p>

             <h3>Childrens Online Privacy Protection Compliance</h3>
             <p>We do not collect any information from anyone under 18 years of age. Our website, content and 
             services are all directed to people who are at least 18 years old or older.</p>

             <h3>Changes to our Privacy Policy</h3>
             <p>If we decide to modify our privacy policy, we will update the Privacy Policy modification 
             date below. This policy was last modified on 03/10/2014.</p>
        </section>

	
	    <section id="terms-of-use" class="page-section">
		   <h1>Terms And Conditions Of Use</h1>
		   <p>PLEASE READ THE TERMS AND CONDITIONS CAREFULLY BEFORE USING THIS SITE.</p>

		   <p>This site is opened to be use by our visitors. In using this site, 
			you the user is agreeing to comply with and be bound by the following terms. After 
			reviewing the terms and conditions thoroughly, if you do not agree with any 
			of them, please do not use this site. The terms and conditions are as follows:</p>
  
			<p>
			   <ul>
				   <li>You are 18 years old or above.</li>
				   <li>You shall take responsibility of any abuse or misuse of information published on this site.</li>
				   <li>This site shall not be held responsible for the implications of your misuse of our information.</li>
				   <li>You shall respect all other users of this site and their views.</li>
				   <li>You shall not attempt to breach the security of this site and it's users' information.</li>
			   </ul>
			</p>
       
			<h3>Acceptance of Agreement. </h3>
			<p>By using this site, you have agreed to the terms and conditions outlined in 
			the Terms Of Use Agreement with respect to our site. 
			This Agreement constitutes the entire and only agreement between you 
			and us, and supersedes all other agreements, representations, warranties 
			and understandings with respect to the site, the contents, distributed documents 
		    or services provided by or listed on the site, and the subject matter 
			of this agreement.</p> 

			<p>This agreement may be amended by us at any time and at any 
			frequency without specific notice to you. The latest agreement will be posted 
			here, and you should review this agreement prior to using this site.</p>

			<h3>Copyright</h3> 
			<p>The content, information, graphics, design and documents related to or published on this site are not for free unless stated otherwise. 
			Any duplication, processing, distribution or any form of utilisation shall require the prior written consent 
			of the author or authors in question.</p>

			<h3>Deleting and Modification</h3>    
			<p>We reserve the right in our sole discretion, without any obligation 
			and without any notice requirement to you, to edit or delete any documents, 
			information or other content appearing on the Site, including this agreement. Modification of this agreement will be 
			published here and you should review it prior to using the site.</p>

			<p>For more insight and information, please <a href="/contact/">contact us</a>.</p>
		</section>
</section>



<aside id="aside"> <!-- aside -->
<?php include_template('aside_raw.php'); ?>
</aside>


<?php include_template('footer.php'); ?>