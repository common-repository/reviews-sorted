const reviewForm = document.getElementById('rs-form_enquiry-form') || false;
if (reviewForm) {
	document.querySelector('.reviews-sorted_form form input[type="hidden"][name="timestamp"]').value = Date.now();
	reviewForm.addEventListener('submit', (e) => {
		e.preventDefault();
		var submit_btn = e.target.querySelector('[type=submit]');
		submit_btn.classList.add('loading');
		form_data = new FormData(e.target);
		fetch(RS_PLUGIN_VARS.ajax_url + '?action=rs_reviews_submit', {
			method: 'POST',
			body: form_data,
		})
			.then(
				(response) => response.json(), // .text(), etc.
			)
			.then((html) => {
				var redirect = form_data.has('redirect')
					? form_data.get('redirect')
					: location.href;
				location.href = redirect;
			});
		return false;
	});
}

// Reviews Layout: Slider
const reviewsSliders = document.querySelectorAll('.reviews-slider.swiper');
if (reviewsSliders.length > 0) {
	const reviewsSwiper = [];

	reviewsSliders.forEach((element, index) => {
		console.log(element.dataset.options)
		let slideOptions = JSON.parse(element.dataset.options);
		reviewsSwiper[`${index}`] = new Swiper(element, slideOptions); 
	})
}

// Reviews Layout: Carousel
const reviewsCarousel = document.querySelector('.reviews-carousel.swiper');
if (reviewsCarousel) {
	let slideOptions = JSON.parse(reviewsCarousel.dataset.options);
	const reviewsCarouselSwiper = new Swiper(reviewsCarousel, slideOptions);
}

// Reviews Layout:  Masonry

function resizeGridItem(item) {
	grid = document.getElementsByClassName('rs-grid-masonry')[0];
	rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));
	rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'));
	rowSpan = Math.ceil(
		(item.querySelector('.rs-grid-masonry .swiper-slide .inner').getBoundingClientRect().height + rowGap) /
			(rowHeight + rowGap),
	);
	item.style.gridRowEnd = 'span ' + rowSpan;
}

function resizeAllGridItems() {
	allItems = document.querySelectorAll('.rs-grid-masonry .swiper-slide');
	for (x = 0; x < allItems.length; x++) {
		resizeGridItem(allItems[x]);
	}
}

function resizeInstance(instance) {
	item = instance.elements[0];
	resizeGridItem(item);
}

window.onload = resizeAllGridItems();
window.addEventListener('resize', resizeAllGridItems);

allItems = document.querySelectorAll('.rs-grid-masonry .swiper-slide');
for (x = 0; x < allItems.length; x++) {
	imagesLoaded(allItems[x], resizeInstance);
}
