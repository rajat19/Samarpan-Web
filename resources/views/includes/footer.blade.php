<!-- copyright section -->
<section id="copyright">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h3>SAMARPAN</h3>
				<br>
				<div class="footer-social">
						<a href="#"><i class="fa fa-facebook"></i></a>
						<a href="#"><i class="fa fa-twitter"></i></a>
						<a href="#"><i class="fa fa-instagram"></i></a>
						<a href="#"><i class="fa fa-youtube-play"></i></a>
						<a href="#"><i class="fa fa-pinterest"></i></a>
						<a href="#"><i class="fa fa-tumblr"></i></a>
				</div>
				<br>
				<p>Copyright © SAMARPAN
                
                | Design: <a href="#">Club-Technocrats</a></p>
			</div>
		</div>
	</div>
</section>

<!-- JAVASCRIPT JS FILES -->
<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery-ui.min.js')}}"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery.parallax.js') }}"></script>
<script src="{{ URL::asset('js/nivo-lightbox.min.js') }}"></script>
<script src="{{ URL::asset('js/smoothscroll.js') }}"></script>
<script src="{{ URL::asset('js/custom.js') }}"></script>
<script src="{{ URL::asset('js/search.js') }}"></script>
<script src="{{ URL::asset('js/tether.min.js') }}"></script>

<script src="{{ URL::asset('js/mdb.min.js') }}""></script>
@if($errors->any())
		@foreach($errors->all() as $error)
			<?php echo "<script type='text/javascript'>toastr['warning']('".$error."')</script>"; ?>
		@endforeach
@endif