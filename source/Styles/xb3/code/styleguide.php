<?php
// template variables modeled after values stored in the $this view object in deploy
$pageTitle = "Style Guide";
$layoutType = "static";
$layoutNavSelected = null;
$layoutSubnavSelected = null;
$head = array();
$isLoggedIn = true;

require_once('includes/header.php');
?>

<!-- $Id: styleguide.php 2988 2009-09-04 15:01:55Z cporto $ -->

<script type="text/javascript">
 STRING = {
    CONFIRM_MSG: "Are you sure you want to click on this?"
 };
 
</script>


<h1>Style Guide</h1>

<ul>
    <li><a href="#Color-Palette">Color Palette</a></li>
    <li><a href="#Data-Grids">Data Grids</a></li>
    <li><a href="#Form-Controls">Form Controls</a></li>
    <li><a href="#Link-Styles">Link Styles</a></li>
    <li><a href="#Project-Conventions">Misc. Project Conventions</a></li>
    <li><a href="#Generic-Markup">Generic Markup</a></li>
</ul>

<h2 id="Color-Palette">Color Palette</h2>

<ul style="color:#000; text-align: center">
    <li style="background-color:#fff;">fff</li>
    <li style="background-color:#ccc;">ccc</li>
    <li style="background-color:#666;">666</li>
    <li style="background-color:#ff0">ff0</li>
    <li style="background-color:#ffe">ffe</li>
    <li style="background-color:#f00">f00</li>
    <li style="background-color:#4992cf">4992cf</li>
    <li style="background-color:#5C8127">5C8127</li>
    <li style="background-color:#8b0000">8b0000</li>
    <li style="background-color:#ffc0cb">ffc0cb</li>
    <li style="background-color:#b8860b">b8860b</li>    
    
</ul>

<h2 id="Data-Grids">Data Grids</h2>

<h2 id="Form-Controls">Form Controls</h2>

<form action="#TBD" method="post">
	<div class="form-row">
		<label for="exText">Text:</label>
		<input type="text" id="exText" />
	</div>

	<div class="form-row">
		<label for="exTextReq">Text Req.:</label>
		<input type="text" id="exTextReq" class="required" />
	</div>

	<div class="form-row">
		<label for="exTextReqErr">Text Req. Error:</label>
		<input type="text" id="exTextReqErr" class="required error"/>
		<label for="username" class="error">This field is required.</label>
	</div>

	<div class="form-row">
		<label for="exPassword">Password Req.:</label>
		<input type="text" id="exPassword" class="required" />
	</div>
	
	<div class="form-row">
		<label for="exPassword">Password Req.:</label>
		<input type="text" id="exPassword" class="required" />
	</div>
	
	<div class="form-row">
		<label for="exTextarea">Textarea:</label>
		<textarea cols="40" rows="6" id="exTextarea"></textarea>
	</div>
	
	<div class="form-row">
		<label for="exSelect">Select:</label>
		<select id="exSelect">
			<option>Single Item Select</option>
			<option>Single Item Select</option>
			<option>Single Item Select</option>
		</select>
	</div>
	
	<div class="form-row">
		<label for="exSelectMulti">Select Multi:</label>
		<select id="exSelectMulti" size="4" multiple="multiple">
			<option>Multiple Item Select</option>
			<option>Multiple Item Select</option>
			<option>Multiple Item Select</option>
			<option>Multiple Item Select</option>
			<option>Multiple Item Select</option>
			<option>Multiple Item Select</option>
		</select>
	</div>
	
	<div class="form-row">
		<span class="setLabel">Radio Options:</span>
		
        <input type="radio" name="radioname" value="val1" id="radioname_val1" />
        <label class="radio" for="radioname_val1">val1</label>
        <input type="radio" name="radioname" value="val2" id="radioname_val2" />
        <label class="radio" for="radioname_val2">val2</label>
	</div>
	
	<div class="form-row combo-group">
		<span class="setLabel">Radio Options w/ Subdata [Combo Group]:</span>
		
        <input type="radio" name="radionameA" value="val1" id="radionameA_val1" />
        <label class="radio" for="radionameA_val1">val1</label>
        <input class="trigger" type="radio" name="radionameA" value="val2" id="radionameA_val2" />
        <label class="radio" for="radionameA_val2">val2 with subdata</label>
        <input type="text" id="subdataid" class="disabled target" disabled="disabled" value="" />
	</div>
	
	<div class="form-row">
		<span class="readonlyLabel">Read Only Value:</span>
		<span class="value">Some Value</span>
	</div>
	
	
	<div class="form-row form-btn">
		<input type="submit" value="Submit" />
		<input type="submit" class="confirm" value="Submit With Confirmation" />
		<input type="button" value="Button" />
		<a href="#TBD" class="btn">a.btn</a>
		
	</div>
	
	
	<h3>Additional States</h3>
	
	<h4>Error Fields - class:error</h4>
	<div>
		<input type="text" class="error" /><br />
		<input type="password" class="error" /><br />
		<input type="radio" class="error" /><br />
		<input type="checkbox" class="error" /><br />
		<textarea class="error" cols="40" rows="6"></textarea><br />
		<select class="error">
			<option>Single Item Select</option>
			<option>Single Item Select</option>
			<option>Single Item Select</option>
		</select><br />

    </div>
    
    
	<h4>Disabled Fields - class:disabled, attr. disabled=disabled</h4>
	<div>
    	<input type="text" class="disabled" disabled="disabled" value="some text" /><br />
    	<input type="password" class="disabled" disabled="disabled" value="some text" /><br />
    	<input type="radio" class="disabled" disabled="disabled" value="some text" /><br />
    	<input type="checkbox" class="disabled" disabled="disabled" value="some text" /><br />
    	<textarea class="disabled" disabled="disabled" cols="40" rows="6">some text</textarea><br />
    	<select class="disabled" disabled="disabled">
    		<option>Single Item Select</option>
    		<option>Single Item Select</option>
    		<option selected="selected">Selected but disabled</option>
    	</select><br />
    </div>
    
    
</form>

<h2 id="Link-Styles">Link Styles</h2>

<ul>
    <li><a href="#TBD">default link [a]</a></li>
    <li><a href="#TBD" class="btn">link as button [a.btn]</a></li>
    <li><a href="index.php" class="confirm">link requiring confirmation [a.confirm]</a></li>
</ul>

<h2 id="Project-Conventions">Misc. Project Conventions</h2>

<h3>Page level messages</h3>

<div class="flash info">
    <p>div.flash.info: Page Level Message / Note</p>
</div>
<div class="flash error">
    <p>div.flash.error: Page Level Error Message</p>
</div>




<h3>table.data</h3>
<table class="data">

	<tr>
		<th>This</th>
		<th>is</th>
		<th>a</th>
		<th>simple header</th>
		<th>row</th>

	</tr>
	<tr>

		<td>This</td>
		<td>is</td>
		<td>a</td>
		<td>simple</td>
		<td>table</td>
	</tr>
	
	<tr>
		<td>This</td>
		<td>is</td>
		<td>a</td>
		<td>simple normal row row row</td>
		<td>your boat</td>

	</tr>
	<tr>
		<th>THeader</th>
		<td>is</td>
		<td>a</td>
		<td>simple</td>
		<td>row</td>

	</tr>
	<tr>
		<td>This</td>
		<td>is</td>
		<td>a</td>
		<td>simple</td>
		<td>row</td>

	</tr>
	<tr>
		<td>This</td>
		<td>is</td>
		<td>a</td>
		<td>simple</td>
		<td>row</td>

	</tr>
</table>



<h2 id="Generic-Markup">Generic Markup</h2>



	<h1>Header Test h1</h1>
	<h2>Header Test h2</h2>
	<h3>Header Test h3</h3>
	<h4>Header Test h4</h4>

	<h5>Header Test h5</h5>
	<h6>Header Test h6</h6>
	<h1>Long Header Test h1 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h1</h1>
	<h2>Long Header Test h2 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h2</h2>

	<h3>Long Header Test h3 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h3</h3>
	<h4>Long Header Test h4 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h4</h4>

	<h5>Long Header Test h5 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h5</h5>
	<h6>Long Header Test h6 &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; This may cause the <a href="#NA">text to wrap</a>. That is the intention. &#8211; h6</h6>

	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
	<p>Epsum factorial non deposit quid pro quo hic escorol. Olypian quarrels et gorilla congolium sic ad nauseum. Souvlaki ignitus carborundum e pluribus unum. Defacto lingo est igpay atinlay. Marquee selectus non provisio incongruous feline nolo contendre. Gratuitous octopus niacin, sodium glutimate. Quote meon an estimate et non interruptus stadium. Sic tempus fugit esperanto hiccup estrogen. Glorious baklava ex librus hup hey ad infinitum. Non sequitur condominium facile et geranium incognito. Epsum factorial non deposit quid pro quo hic escorol. Marquee selectus non provisio incongruous feline nolo contendre Olypian quarrels et gorilla congolium sic ad nauseum. Souvlaki ignitus carborundum e pluribus unum.</p>
	<ul>
		<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. </li>
		<li>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Epsum factorial non deposit quid pro quo hic escorol.</li>
		<li>Souvlaki ignitus carborundum e pluribus unum. </li>

	</ul>
	<ol>
		<li>Defacto lingo est igpay atinlay. Marquee selectus non provisio incongruous feline nolo contendre. </li>
		<li>Quote meon an estimate et non interruptus stadium.
	<ol>
		<li>Lorem ipsum</li>
		<li>Dolor sit
	<ul>
		<li>Consectetuer</li>

		<li>Adipiscing elit</li>
	</ul></li>
		<li>Diam</li>
	</ol></li>
		<li>Sic tempus fugit esperanto hiccup estrogen. Glorious baklava ex librus hup hey ad infinitum. </li>
		<li>Epsum factorial non deposit quid pro quo hic escorol. </li>
	</ol>

	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
<pre>
pre
pre
pre
pre     pre
</pre>
	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
<pre><code>&lt;meta name="robots" content="noindex" /&gt;</code></pre>
	<h2>Header Test &#8211; the following is a test of numerous inline tags &#8211; h2</h2>

	<p>Epsum factorial non deposit quid pro quo hic escorol, <a href="http://local.placenamehere.com/link">link</a>. Olypian quarrels et gorilla congolium sic ad nauseum. <acronym title="Acronym Test">ACRO</acronym> Souvlaki ignitus<sup><a href="#fn12981848494416dc5dc7453">1</a></sup> carborundum e pluribus unum. Defacto lingo est igpay atinlay. Marquee selectus non provisio incongruous feline <del>del nolo</del> contendre. Gratuitous octopus niacin, sodium glutimate. Quote meon an estimate et non interruptus stadium. Sic tempus fugit esperanto hiccup estrogen. <ins>Glorious baklava insert</ins> ex librus<sup><a href="#fn1106312694416dc5dc75db">2</a></sup> hup hey ad infinitum. Non sequitur condominium facile et geranium incognito. Epsum factorial non deposit quid pro quo hic escorol. <em>Not a Marquee but emphasis</em> selectus non provisio incongruous feline nolo contendre Olypian quarrels et gorilla congolium sic ad nauseum. <strong>Strong Souvlaki</strong> ignitus carborundum e pluribus unum.</p>

	<p>Lorem ipsum dolor sit amet, <q>quote consectetuer adipiscing elit</q>, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. <abbr title="abbreviation test">Abbr.</abbr> Ut wisi enim ad minim veniam, quis nostrud<sup><a href="#fn17763587224416dc5dc76c4">3</a></sup> exerci<sup><a href="#fn14102765224416dc5dc78ed">4</a></sup> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure <code>code dolor</code> in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore <cite>cite: te feugait nulla facilisi.[5]</cite></p>

 <blockquote>
	<p>Li Europan lingues es membres del sam familie. Lor separat existentie es un myth. Por scientie, musica, sport etc, li tot Europa usa li sam vocabularium. Li lingues differe solmen in li grammatica, li pronunciation e li plu commun vocabules. Omnicos directe al desirabilita; de un nov lingua franca: on refusa continuar payar custosi traductores. It solmen va esser necessi far uniform grammatica, pronunciation e plu sommun paroles.</p>
	<p>Ma quande lingues coalesce, li grammatica del resultant lingue es plu simplic e regulari quam ti del coalescent lingues. Li nov lingua franca va esser plu simplic e regulari quam li existent Europan lingues. It va esser tam simplic quam Occidental: in fact, it va esser Occidental. A un Angleso it va semblar un simplificat Angles, quam un skeptic Cambridge amico dit me que Occidental es.</p>
 </blockquote>
	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
 <blockquote cite="test">
	<ul>

		<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. </li>
		<li>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Epsum factorial non deposit quid pro quo hic escorol.</li>
	</ul>
 </blockquote>
 <blockquote>
	<ol>
		<li>Defacto lingo est igpay atinlay. Marquee selectus non provisio incongruous feline nolo contendre. </li>

		<li>Quote meon an estimate et non interruptus stadium.</li>
	</ol>
 </blockquote>
	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
 <dl>
<dt>example</dt>
    <dd>One that is representative of a group as a whole</dd>

    <dd>One serving as a pattern of a specific kind</dd>
<dt>item</dt>
    <dd>A single article or unit in a collection, enumeration, or series.</dd>
 </dl>
	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
	<table>
		<tr>

			<td>This</td>
			<td>is</td>
			<td>a</td>
			<td>simple</td>
			<td>table</td>
		</tr>

		<tr>
			<th>This</th>
			<th>is</th>
			<th>a</th>
			<th>simple header</th>
			<th>row</th>

		</tr>
		<tr>
			<td>This</td>
			<td>is</td>
			<td>a</td>
			<td>simple normal row row row</td>
			<td>your boat</td>

		</tr>
		<tr>
			<th>THeader</th>
			<td>is</td>
			<td>a</td>
			<td>simple</td>
			<td>row</td>

		</tr>
		<tr>
			<td>This</td>
			<td>is</td>
			<td>a</td>
			<td>simple</td>
			<td>row</td>

		</tr>
		<tr>
			<td>This</td>
			<td>is</td>
			<td>a</td>
			<td>simple</td>
			<td>row</td>

		</tr>
	</table>
	<p class="note">Note: Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
<table>
<caption>Table caption</caption>
<tr>
<td>This</td>
<td>is</td>
<td>a</td>

<td>simple</td>
<td>table</td>
</tr>
<tr>
<th>This</th>
<th>is</th>
<th>a</th>
<th>simple header</th>
<th>row</th>
</tr>

<tr>
<td>This</td>
<td>is</td>
<td>a</td>
<td>simple normal row row row</td>
<td>your boat</td>
</tr>
</table>
<address><a href="#NA">Address of Author</a>
5 Happy St.
</address>
<h6>Header Test &#8211; the following is a test of numerous inline tags &#8211; h6</h6>
<p class="footnote" id="fn12981848494416dc5dc7453"><sup>1</sup> Defacto lingo est igpay atinlay. Marquee selectus non provisio incongruous feline nolo contendre. </p>
<p class="footnote" id="fn1106312694416dc5dc75db"><sup>2</sup> Quote meon an estimate et non interruptus stadium.</p>
<p class="footnote" id="fn17763587224416dc5dc76c4"><sup>3</sup> Lorem ipsum</p>
<p class="footnote" id="fn14102765224416dc5dc78ed"><sup>4</sup> <a href="#NA">Dolor sit</a></p>
<p class="footnote" id="fn5"><sup>5</sup> Consectetuer</p>



<?php require_once('includes/footer.php'); ?>
