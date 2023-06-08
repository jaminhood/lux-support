$(document).ready(function () {
	/* === Database Table Scripts === */
	$('#database-table').dataTable({
		columnDefs: [{ sortable: true }],
	});
	/* === Colapsable Scripts === */
	$('[data-collapse]').each(function () {
		const me = $(this),
			target = me.data('collapse');

		me.click(function () {
			$(target).collapse('toggle');
			$(target).on('shown.bs.collapse', function () {
				me.html('<i class="fas fa-minus"></i>');
			});
			$(target).on('hidden.bs.collapse', function () {
				me.html('<i class="fas fa-plus"></i>');
			});
			return false;
		});
	});

	/* === Image Custom Uploader Scripts === */
	const uri = window.location.href;
	const add_news = 'admin.php?page=lux-top-news&tab=add';
	const update_news = 'admin.php?page=lux-top-news&tab=update';
	if (uri.includes(add_news) || uri.includes(update_news)) {
		const add_btn = document.getElementById('news_image');
		const news_id = document.getElementById('news_image_id');
		const news_label = document.getElementById('news_image_label');
		const news_uploader = wp.media({
			title: 'Choose News',
			button: {
				text: 'Use this Image',
			},
			multiple: false,
		});
		add_btn.addEventListener(`click`, e => {
			e.preventDefault();
			if (news_uploader) {
				news_uploader.open();
			}
		});
		news_uploader.on(`select`, () => {
			const attachment = news_uploader
				.state()
				.get(`selection`)
				.first()
				.toJSON();
			news_label.textContent = attachment.url;
			news_id.setAttribute(`value`, attachment.id);
		});
	}

	const add_giftcard = 'admin.php?page=lux-giftcards&tab=create-sub-category';
	const add_giftcard_c = 'admin.php?page=lux-giftcards&tab=create-category';
	const update_giftcard =
		'admin.php?page=lux-giftcards&tab=update-sub-category';

	if (
		uri.includes(add_giftcard) ||
		uri.includes(update_giftcard) ||
		uri.includes(add_giftcard_c)
	) {
		const add_btn = $('#icon');
		const icon_id = $('#icon-id');
		const icon_label = $('#icon-label');
		console.log(add_btn);
		const icon_uploader = wp.media({
			title: 'Choose Icon',
			button: {
				text: 'Use this Image',
			},
			multiple: false,
		});
		add_btn.on(`click`, e => {
			e.preventDefault();
			if (icon_uploader) {
				icon_uploader.open();
			}
		});
		icon_uploader.on(`select`, () => {
			const attachment = icon_uploader
				.state()
				.get(`selection`)
				.first()
				.toJSON();
			icon_label.text(attachment.url);
			icon_id.attr(`value`, attachment.id);
		});
	}

	const add_barcode = 'admin.php?page=lux-barcode&tab=add-new';
	const update_barcode = 'admin.php?page=lux-barcode&tab=update';
	if (uri.includes(add_barcode) || uri.includes(update_barcode)) {
		const asset_type = $(`#asset-type`);
		const asset = $(`#asset`);
		const barcode_img = $(`#barcode`);
		const barcode_label = $(`#barcode-label`);
		const barcode_id = $(`#barcode-id`);
		// const news_id = document.getElementById("news_image_id");
		// const news_label = document.getElementById("news_image_label");
		// btn.addClass(`disabled`);

		const barcode_uploader = wp.media({
			title: 'Select Barcode',
			button: {
				text: 'Use this Barcode',
			},
			multiple: false,
		});

		barcode_img.on(`click`, e => {
			e.preventDefault();
			if (barcode_uploader) {
				barcode_uploader.open();
			}
		});

		barcode_uploader.on(`select`, () => {
			const attachment = barcode_uploader
				.state()
				.get(`selection`)
				.first()
				.toJSON();
			barcode_label.text(attachment.url);
			barcode_id.attr(`value`, attachment.id);
		});

		const fetch_assets = recipient => {
			return new Promise((resolve, reject) => {
				$.ajax({
					url: script_links.ajaxurl, // The wordpress Ajax URL echoed on line 4
					data: {
						// The action is the WP function that'll handle this ajax request
						action: recipient,
					},
					success: data => {
						if (data.data.length > 0) {
							const assets = data.data.map(cur => {
								const { id, name, short_name } = cur;
								return { id, name, short_name };
							});
							resolve(assets);
						}
					},
					error: errorThrown => {
						reject();
						throw new Error(errorThrown);
					},
				});
			});
		};

		asset_type.on(`change`, async e => {
			const id = e.target.value;
			let curr = ``;
			if (id == 1) {
				curr = await fetch_assets(`hid_ex_m_get_e_assets_with_local_bank`);
			} else if (id == 2) {
				curr = await fetch_assets(`hid_ex_m_get_crypto_assets_with_local_bank`);
			}
			if (curr !== ``) {
				asset.html(
					curr.map(
						currency =>
							`<option value="${currency.id}">${currency.name} | ${currency.short_name}</option>`,
					),
				);
			}
		});
	}

	const credit_wallet = 'admin.php?page=lux-credit-wallet';
	const debit_wallet = 'admin.php?page=lux-debit-wallet';
	if (uri.includes(credit_wallet) || uri.includes(debit_wallet)) {
		$(`.select-display`).on(`click`, () => {
			$(`.select-dropdown`).toggleClass(`active`);
		});

		let lis = [...$(`.select-list li`)];

		const getData = async () =>
			await [...$(`.select-list li`)].map(e => {
				return {
					id: Number(e.dataset.value),
					name: e.textContent,
				};
			});

		const getDatas = async id =>
			await lis
				.filter(e => e.textContent.includes(id))
				.map(e => {
					return {
						id: Number(e.dataset.value),
						name: e.textContent,
					};
				});

		$(`#select-search`).on(`input`, e => {
			getDatas(e.target.value)
				.then(data => {
					let output = ``;
					data.forEach(da => {
						if (da.name.includes(e.target.value)) {
							output += `<li data-value="${da.id}">${da.name}</li>`;
						}
					});
					$(`.select-list`).html(output);
					return [...$(`.select-list li`)];
				})
				.then(lis => {
					lis.forEach(li =>
						li.addEventListener(`click`, e => {
							$(`#customer_name`).attr(`value`, e.target.dataset.value);
							$(`.select-render`).html(e.target.textContent);
							$(`.select-dropdown`).toggleClass(`active`);
						}),
					);
				});
		});

		getData().then(() => {
			lis.forEach(li =>
				li.addEventListener(`click`, e => {
					$(`#customer_name`).attr(`value`, e.target.dataset.value);
					$(`.select-render`).html(e.target.textContent);
					$(`.select-dropdown`).toggleClass(`active`);
				}),
			);
		});
	}
});
jQuery(document).ready(function ($) {
	const string = window.location.href;

	const buy_order = 'admin.php?page=lux-buy&tab=create-new';
	const sell_order = 'admin.php?page=lux-sell&tab=create-new';

	const substring = 'admin.php?page=e-currency-management&tab=create-new';

	const substring2 = 'admin.php?page=crypto-currency-management&tab=create-new';

	const giftcard = 'admin.php?page=giftcard-management&tab=create-new';

	let update_giftcard =
		'admin.php?page=giftcard-management&tab=update-giftcard';

	const substring3 =
		'admin.php?page=crypto-currency-management&tab=update-crypto-currency';

	const substring4 =
		'admin.php?page=e-currency-management&tab=update-e-currency';

	let update_sell_order = 'admin.php?page=lux-sell&tab=update-sell-order';

	let update_buy_order = 'admin.php?page=lux-buy&tab=update-buy-order';

	let support_chat = 'admin.php?page=support&tab=chat';

	const condition =
		string.includes(substring) ||
		string.includes(substring2) ||
		string.includes(substring3) ||
		string.includes(substring4) ||
		string.includes(buy_order) ||
		string.includes(update_buy_order) ||
		string.includes(sell_order) ||
		string.includes(update_sell_order) ||
		string.includes(giftcard) ||
		string.includes(update_giftcard);

	if (condition) {
		let addButton = document.getElementById('image-select-button');
		let deleteButton = document.getElementById('image-delete-button');
		let img = document.getElementById('asset-image-tag');
		let hidden = document.getElementById('icon-media-id');

		let customUploader = wp.media({
			title: 'Choose the Asset Icon',
			button: {
				text: 'Use this Image',
			},
			multiple: false,
		});

		addButton.addEventListener('click', function () {
			if (customUploader) {
				customUploader.open();
			}
		});

		customUploader.on('select', function () {
			let attachment = customUploader.state().get('selection').first().toJSON();

			img.setAttribute('src', attachment.url);
			img.setAttribute('style', 'max-width:200px');
			img.setAttribute('style', 'max-height:200px');

			hidden.setAttribute('value', attachment.id);
		});

		deleteButton.addEventListener('click', function () {
			img.removeAttribute('src');
			hidden.removeAttribute('value');
		});
	}

	const condition2 = string.includes(buy_order); // || string.includes(sell_order);

	if (condition2) {
		let all_assets_rates = {};
		let current_rate = 0;
		let hidden_field = document.getElementById('hidden-rate');

		$.fn.retrieve_data_ajax = function (recipient) {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: function (data) {
					if (data['data'].length > 0) {
						// console.log(data['data']);

						let outputhtml = '';

						data['data'].forEach(element => {
							outputhtml +=
								'<option rate=' +
								element['selling_price'] +
								' value=' +
								element['id'] +
								'>' +
								element['name'] +
								' | ' +
								element['short_name'] +
								'</option>';

							all_assets_rates[element['id']] = element['selling_price'];
						});

						$('#select-asset').html(outputhtml);

						current_rate = all_assets_rates[select_field.value];

						hidden_field.value = current_rate;
					}
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		};

		$.fn.update_select_input = function (result) {
			if (result.length === 0) {
				console.log(result);
			}
		};

		$('.asset-btn-1').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		});

		$('.asset-btn-2').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		});

		// Fee and quantity auto calculate
		let quantity_field = document.getElementById('quantity');

		// let fee_field = document.getElementById("fee");

		let select_field = document.getElementById('select-asset');

		let rate_output = document.getElementById('rate-output');

		let fee_hidden = document.getElementById('hidden-fee');

		let fee_output = document.getElementById('fee');

		select_field.addEventListener('change', function () {
			current_rate = all_assets_rates[select_field.value];
			hidden_field.value = current_rate;
		});

		quantity_field.addEventListener('input', function () {
			fee_hidden.value = current_rate * quantity_field.value;

			fee_output.innerHTML = fee_hidden.value;

			rate_output.innerHTML = current_rate;
		});

		// console.log(hidden_field.value);
		// console.log(fee_field);
		// console.log(select_field);
	}

	const condition5 = string.includes(sell_order);

	if (condition5) {
		let all_assets_rates = {};
		let current_rate = 0;
		let hidden_field = document.getElementById('hidden-rate');

		$.fn.retrieve_data_ajax = function (recipient) {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: function (data) {
					if (data['data'].length > 0) {
						// console.log(data['data']);

						let outputhtml = '';

						data['data'].forEach(element => {
							outputhtml +=
								'<option rate=' +
								element['buying_price'] +
								' value=' +
								element['id'] +
								'>' +
								element['name'] +
								' | ' +
								element['short_name'] +
								'</option>';

							all_assets_rates[element['id']] = element['buying_price'];
						});

						$('#select-asset').html(outputhtml);

						current_rate = all_assets_rates[select_field.value];

						hidden_field.value = current_rate;
					}
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		};

		$.fn.update_select_input = function (result) {
			if (result.length === 0) {
				console.log(result);
			}
		};

		$('.asset-btn-1').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		});

		$('.asset-btn-2').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		});

		// Fee and quantity auto calculate
		let quantity_field = document.getElementById('quantity');

		// let fee_field = document.getElementById("fee");

		let select_field = document.getElementById('select-asset');

		let rate_output = document.getElementById('rate-output');

		let fee_hidden = document.getElementById('hidden-fee');

		let fee_output = document.getElementById('fee');

		select_field.addEventListener('change', function () {
			current_rate = all_assets_rates[select_field.value];
			hidden_field.value = current_rate;
		});

		quantity_field.addEventListener('input', function () {
			fee_hidden.value = current_rate * quantity_field.value;

			fee_output.innerHTML = fee_hidden.value;

			rate_output.innerHTML = current_rate;
		});

		// console.log(hidden_field.value);
		// console.log(fee_field);
		// console.log(select_field);
	}

	const condition3 = string.includes(update_buy_order); //|| string.includes(update_sell_order);

	if (condition3) {
		let all_assets_rates = {};
		let current_rate = 0;
		let hidden_field = document.getElementById('hidden-rate');
		let asset_type = document.getElementById('asset-type');
		let asset_id = document.getElementById('asset-id');

		$.fn.retrieve_data_ajax = function (recipient) {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: function (data) {
					if (data['data'].length > 0) {
						// console.log(data['data']);

						let outputhtml = '';

						data['data'].forEach(element => {
							let selects = element['id'] == asset_id.value ? ' selected' : '';

							outputhtml +=
								'<option rate=' +
								element['selling_price'] +
								' value=' +
								element['id'] +
								selects +
								'>' +
								element['name'] +
								' | ' +
								element['short_name'] +
								'</option>';

							all_assets_rates[element['id']] = element['selling_price'];
						});

						$('#select-asset').html(outputhtml);

						current_rate = all_assets_rates[select_field.value];

						hidden_field.value = current_rate;
					}
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		};

		if (asset_type.value == 1) {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		} else if (asset_type.value == 2) {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		}

		$('.asset-btn-1').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		});

		$('.asset-btn-2').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		});

		// Fee and quantity auto calculate
		let quantity_field = document.getElementById('quantity');

		// let fee_field = document.getElementById("fee");

		let select_field = document.getElementById('select-asset');

		let rate_output = document.getElementById('rate-output');

		let fee_hidden = document.getElementById('hidden-fee');

		let fee_output = document.getElementById('fee');

		let fee_temp = fee_hidden.value / quantity_field.value;

		rate_output.innerHTML = fee_temp.toFixed(2);

		select_field.addEventListener('change', function () {
			current_rate = all_assets_rates[select_field.value];
			hidden_field.value = current_rate;
		});

		quantity_field.addEventListener('input', function () {
			let price = current_rate * quantity_field.value;

			fee_hidden.value = price.toFixed(2);

			fee_output.innerHTML = fee_hidden.value;

			rate_output.innerHTML = current_rate;
		});

		// console.log(asset_type.value);
		// console.log(asset_id.value);
	}

	const condition6 = string.includes(update_sell_order);

	if (condition6) {
		let all_assets_rates = {};
		let current_rate = 0;
		let hidden_field = document.getElementById('hidden-rate');
		let asset_type = document.getElementById('asset-type');
		let asset_id = document.getElementById('asset-id');

		$.fn.retrieve_data_ajax = function (recipient) {
			$.ajax({
				url: script_data.ajaxurl, // The wordpress Ajax URL echoed on line 4
				data: {
					// The action is the WP function that'll handle this ajax request
					action: recipient,
				},
				success: function (data) {
					if (data['data'].length > 0) {
						// console.log(data['data']);

						let outputhtml = '';

						data['data'].forEach(element => {
							let selects = element['id'] == asset_id.value ? ' selected' : '';

							outputhtml +=
								'<option rate=' +
								element['buying_price'] +
								' value=' +
								element['id'] +
								selects +
								'>' +
								element['name'] +
								' | ' +
								element['short_name'] +
								'</option>';

							all_assets_rates[element['id']] = element['buying_price'];
						});

						$('#select-asset').html(outputhtml);

						current_rate = all_assets_rates[select_field.value];

						hidden_field.value = current_rate;
					}
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		};

		if (asset_type.value == 1) {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		} else if (asset_type.value == 2) {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		}

		$('.asset-btn-1').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_e_assets');
		});

		$('.asset-btn-2').click(function () {
			$.fn.retrieve_data_ajax('hid_ex_m_get_crypto_assets');
		});

		// Fee and quantity auto calculate
		let quantity_field = document.getElementById('quantity');

		// let fee_field = document.getElementById("fee");

		let select_field = document.getElementById('select-asset');

		let rate_output = document.getElementById('rate-output');

		let fee_hidden = document.getElementById('hidden-fee');

		let fee_output = document.getElementById('fee');

		let fee_temp = fee_hidden.value / quantity_field.value;

		rate_output.innerHTML = fee_temp.toFixed(2);

		select_field.addEventListener('change', function () {
			current_rate = all_assets_rates[select_field.value];
			hidden_field.value = current_rate;
		});

		quantity_field.addEventListener('input', function () {
			let price = current_rate * quantity_field.value;

			fee_hidden.value = price.toFixed(2);

			fee_output.innerHTML = fee_hidden.value;

			rate_output.innerHTML = current_rate;
		});

		// console.log(asset_type.value);
		// console.log(asset_id.value);
	}

	const condition4 = string.includes(support_chat);

	if (condition4) {
		let addButton = document.getElementById('attachment-select-button');
		let deleteButton = document.getElementById('attachment-delete-button');
		let att_name = document.getElementById('attachment-name');
		let hidden = document.getElementById('attachment-id');

		let customUploader = wp.media({
			title: 'Choose the File To Attach',
			button: {
				text: 'Select Attachment',
			},
			multiple: false,
		});

		addButton.addEventListener('click', function () {
			if (customUploader) {
				customUploader.open();
			}
		});

		customUploader.on('select', function () {
			let attachment = customUploader.state().get('selection').first().toJSON();

			att_name.setAttribute('src', attachment.url);

			let file_name = attachment.filename;

			let display_file_name =
				file_name.length >= 40
					? file_name.slice(0, 15) + '...' + file_name.slice(-15)
					: file_name;

			att_name.innerHTML = display_file_name;

			hidden.setAttribute('value', attachment.id);
		});

		deleteButton.addEventListener('click', function () {
			att_name.innerHTML = '';
			hidden.removeAttribute('value');
		});

		// Form Submission
		let chat_form = document.getElementById('new-admin-chat-form');

		chat_form.addEventListener('submit', function (e) {
			e.preventDefault();

			let message_body = document.getElementById('message-body').value;

			let attachment_id = document.getElementById('attachment-id').value;

			let ticket_id = document.getElementById('ticket-id').value;

			let error_element = document.getElementById('empty-chat-error');

			if (!message_body) {
				message_body = '';
			}

			if (!attachment_id) {
				attachment_id = 0;
			}

			if (!message_body && !attachment_id) {
				error_element.innerHTML = 'Cannot Send Empty Message';
				error_element.style.color = 'red';
				return;
			}

			error_element.innerHTML = '';

			// console.log(attachment,message_body,ticket_id);

			let data = {
				sender: 'Admin',
				message: message_body,
				attachment: attachment_id,
				ticket: ticket_id,
			};

			$.ajax({
				url: script_data.ajaxurl,

				data: {
					action: 'hid_ex_m_add_new_chat',
					data: data,
				},

				success: function () {
					document.getElementById('message-body').value = '';

					att_name.innerHTML = '';
					hidden.removeAttribute('value');
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		});

		let chat_time_sent = document.getElementsByClassName('time-sent');

		setInterval(function () {
			let last_chat_time = chat_time_sent[chat_time_sent.length - 1].innerHTML;

			$.ajax({
				url: script_data.ajaxurl,

				data: {
					action: 'hid_ex_m_get_recent_chats_view',
					time: last_chat_time,
					ticket_id: document.getElementById('ticket-id').value,
				},

				success: function (data) {
					if (data['data'] != 0) {
						if (document.getElementById('zero-chats-notice')) {
							document.getElementById('zero-chats-notice').remove();
						}

						let chat_wrapper = document.getElementById(
							'chats-messagges-wrapper',
						);

						let build_string = '';

						data['data'].forEach(msg => {
							let sender_class =
								msg['sender'] == 'Admin' ? 'admin-chat' : 'customer-chat';

							let sender = msg['sender'] == 'Admin' ? 'Admin' : 'Customer';

							build_string +=
								'<div class="single-chat-message ' + sender_class + '">';

							if (msg['message'] != '') {
								build_string +=
									'<p class="message-body">' +
									msg['message'].replace('\\', '') +
									'</p>';
							}

							if (msg['attachment'] != 0) {
								build_string +=
									'<a href="' +
									msg['attachment_url'] +
									'" target="_blank">Click Here to View Attachment</a>';
							}

							build_string +=
								'<span class="message-details">Sent by <span class="message-sender">' +
								sender +
								'</span> | <span class="time-sent">' +
								msg['time_stamp'] +
								'</span></span></div>';
						});

						chat_wrapper.innerHTML += build_string;
					}
				},
				error: function (errorThrown) {
					window.alert(errorThrown);
				},
			});
		}, 3000);
	}
});
