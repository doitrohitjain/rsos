
<!--
<div class="spinner-container mainCls11 custom-loader-old loader-old centered-old hide">
	<div class="loader-main">
		<div class="cssload-loader"><div class="cssload-inner cssload-one"></div>
			<div class="cssload-inner cssload-two"></div><div class="cssload-inner cssload-three"></div>
		</div>
		<p>
			Please wait ...
		</p>
	</div>
</div> 
--> 

  
<div class="custom-loader mainCls loader hide ">
	<div class="cssload-loader">
		<div class="cssload-inner cssload-one"></div>
		<div class="cssload-inner cssload-two"></div>
		<div class="cssload-inner cssload-three"></div>
	</div> 
</div>

<style> 
.custom-loader {
	width: 120px;
	height: 120px;
	color: #03a9f4; 
	background:
	linear-gradient(currentColor 0 0) 100%  0,
	linear-gradient(currentColor 0 0) 0  100%;
	background-size: 50.1% 50.1%;
	background-repeat: no-repeat;
	animation:  f7-0 0.5s infinite linear;
	position: fixed;
	top: 45%;
	left: 45%;
	transform: translate(-50%, -50%);
}
.custom-loader::before,
.custom-loader::after {
  content:"";
  position: absolute;
  inset:0 50% 50% 0;
  background:currentColor;
  transform:scale(var(--s,1)) perspective(300px) rotateY(0deg);
  transform-origin: bottom right; 
  animation: f7-1 0.25s infinite linear alternate;
}
.custom-loader::after {
  --s:-1,-1;
} 
@keyframes f7-0 {
  0%,49.99% {transform: scaleX(1)  rotate(0deg)}
  50%,100%  {transform: scaleX(-1) rotate(-90deg)}
} 
@keyframes f7-1 {
  49.99% {transform:scale(var(--s,1)) perspective(300px) rotateX(-90deg) ;filter:grayscale(0)}
  50%    {transform:scale(var(--s,1)) perspective(300px) rotateX(-90deg) ;filter:grayscale(0.8)}
  100%   {transform:scale(var(--s,1)) perspective(300px) rotateX(-180deg);filter:grayscale(0.8)}
}

</style>
