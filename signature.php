<?php
	function checkSignature() {
		if (isset($_POST['output'])) {
		}
	}
	
	function addSignatureForm() {
	?>
		  <link rel="stylesheet" href="build/jquery.signaturepad.css">
		  <!--[if lt IE 9]><script src="build/flashcanvas.js"></script><![endif]-->
		  <script src="build/jquery.signaturepad.min.js"></script>
		  <script src="build/json2.min.js"></script>
			  <div class="sigPad">
			    <label for="name">Print your name</label>
			    <input type="text" name="name" id="name" class="name">
			    <p class="typeItDesc">Review your signature</p>
			    <p class="drawItDesc">Draw your signature</p>
			    <ul class="sigNav">
			      <li class="typeIt"><a href="#type-it" class="current">Type It</a></li>
			      <li class="drawIt"><a href="#draw-it" >Draw It</a></li>
			      <li class="clearButton"><a href="#clear">Clear</a></li>
			    </ul>
			    <div class="sig sigWrapper">
			      <div class="typed"></div>
			      <canvas class="pad" width="198" height="55"></canvas>
			      <input type="hidden" name="output" class="output">
			      <input type="hidden" id="signatureid" name="signatureid">
			    </div>
			  </div>
<?php			
	}
?>
