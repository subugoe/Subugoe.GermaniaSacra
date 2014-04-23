$.fn.extend({

//  Eintragsliste anzeigen
	populate_liste: function() {
	
		var $this = $(this);

		url = "kloster/jsonlist";
		$.getJSON(url, function(response){
			var ortGemeindeKreisArray = response[1];
			var $inputOrtGemeindeKreis = $("select[name='ort[]']");
			$.each(ortGemeindeKreisArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputOrtGemeindeKreis.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bearbeitungsstatusArray = response[2];
			var $inputBearbeitungsstatus = $("select[name='bearbeitungsstatus[]']");
			$.each(bearbeitungsstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});
			var kloster = response[0];
			$.each(kloster, function (key, value) {
				var tr = $this.find('tr:eq(1)').clone(true);
				tr.find('td :input').each( function() {
					var name = $(this).attr('name');
					if ( typeof name === 'undefined' ) return;
					name = name.replace('[]', '');
					var val = response[0][key][name];
					if ( $(this).is('[type=checkbox]') ) {
						return;
					} else if ( $(this).is('select') ) {
						if(name=="ort"){
							if (val.length > 1) {
								$.each(val, function (ortkey, ortvalue) {
								var selector = '#selectId' + (key+1);
								tr.find("select[name='ort[]'] option").each(function( i, opt ) {
								    if( opt.value == ortvalue ) {
								        $(opt).attr('selected', 'selected');
								    }
								});
								})
							}
							else {
								tr.find("select[name='ort[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}
						}
						else if (name=="bearbeitungsstatus") {
							tr.find("select[name='bearbeitungsstatus[]'] option").each(function( i, opt ) {
								if( opt.value == val ) {
									$(opt).attr('selected', 'selected');
								}
							});
						}
						else {
							$(this).append('<option>' + val + '</option>');
						}
					} else {
						$(this).val( val );
					}
				});
				$this.append(tr);
				var url= "kloster/edit/" + value.uuid;
				var id = "editLink" + (key+1);
				$this.find('a#editLink:eq(1)').attr('href', url);
				$this.find('a#editLink:eq(1)').attr('id', id);
			});
			$this.find("textarea").autosize();
		});
		
	
	},

// Das Formular zum Editiren mit Daten des jeweiligen Eintrages ausfüllen
	populate_kloster: function(index, url) {

		var $this = $(this);

		$.getJSON(url, function(response){

			var bearbeitungsstatusArray = response[1];
			var $inputBearbeitungsstatus = $("select[name='bearbeitungsstatus']");
			$inputBearbeitungsstatus.empty();
			$.each(bearbeitungsstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var personallistenstatusArray = response[2];
			var $inputPersonallistenstatus = $("select[name='personallistenstatus']");
			$inputPersonallistenstatus.empty();
			$.each(personallistenstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputPersonallistenstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bandArray = response[3];
			var $inputBand = $("select[name='band']");
			$inputBand.empty();
			$inputBand.append($("<option>", { value: '', html: 'Kein Band' }));
			$.each(bandArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBand.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var literaturArray = response[4];
			var $inputLiteratur = $("select[name='literatur[]']");
			$inputLiteratur.empty();
			$.each(literaturArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputLiteratur.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bistumArray = response[5];
			var $inputBistum = $("select[name='bistum[]']");
			$inputBistum.empty();
			$.each(bistumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBistum.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var ordenArray = response[6];
			var $inputOrden = $("select[name='orden[]']");
			$inputOrden.empty();
			$.each(ordenArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputOrden.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var klosterstatusArray = response[7];
			var $inputKlosterstatus = $("select[name='klosterstatus[]']");
			$inputKlosterstatus.empty();
			$.each(klosterstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputKlosterstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var zeitraumArray = response[8];
			var $inputZeitraum = $("select[name='orden_zeitraum[]']");
			$inputZeitraum.empty();
			$.each(zeitraumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputZeitraum.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var zeitraumArray = response[8];
			var $inputZeitraum = $("select[name='klosterstandort_zeitraum[]']");
			$inputZeitraum.empty();
			$.each(zeitraumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputZeitraum.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var klosterstandorte = response[0].klosterstandorte;
			var klosterorden = response[0].klosterorden;
			var klosterurl = response[0].url;
			var klosterliteratur = response[0].literatur;


			var uuid = response[0].uuid;
			var update_url =  "kloster/update/" + uuid;
			$('#EditKloster').attr("action", update_url);

			var fieldset = $this.find('fieldset:eq(0)');
			fieldset.find('label :input').each( function() {
				var name = $(this).attr('name');
				if ( typeof name === 'undefined' ) return;
				name = name.replace('[]', '');
				var val = response[0][name];
				if ( $(this).is('[type=checkbox]') ) {
					if (name == "wuestung" && val==1) {
						$(this).prop('checked', true);
					}
					else {
						return;
					}
				}
				else if ( $(this).is('select') ) {

					if (name=="bearbeitungsstatus") {
						fieldset.find("select[name='bearbeitungsstatus'] option").each(function( i, opt ) {
							if( opt.value == val ) {
								$(opt).attr('selected', 'selected');
							}
						});
					}
					else if (name=="personallistenstatus") {
						fieldset.find("select[name='personallistenstatus'] option").each(function( i, opt ) {
							if( opt.value == val ) {
								$(opt).attr('selected', 'selected');
							}
						});
					}
					else if (name=="band") {
						fieldset.find("select[name='band'] option").each(function( i, opt ) {
							if( opt.value == val ) {
								$(opt).attr('selected', 'selected');
							}
						});
					}
					else {

						$(this).find('option').remove();
						if ( val ) $(this).append('<option>' + val + '</option>');
					}
				}
				else {
						$(this).val( val );
				}
			});

			$this.find("input[type=url]").keyup();

			$.each(klosterorden, function (key, value) {
				if (key == 0) {
					var fieldset = $this.find('fieldset:eq(2)');
				}
				else {
					var fieldset = $this.find('div.multiple:eq(2)').clone(true);
				}

				fieldset.find('label :input').each( function() {
						var name = $(this).attr('name');
						if ( typeof name === 'undefined' ) return;
						name = name.replace('[]', '');
						var val = value[name];
						if ( $(this).is('[type=checkbox]') ) {
							return;
						} else if ( $(this).is('select') ) {

							if (name== "orden") {
								$this.find("select[name='orden[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}
							else if (name== "klosterstatus") {
								$this.find("select[name='klosterstatus[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}
							else if (name== "orden_zeitraum") {
								$this.find("select[name='orden_zeitraum[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}


						} else {
							$(this).val( val );
						}
					});

					if (key > 0) {
						$this.find('div.multiple:eq(2)').append(fieldset);
					}
					$this.find("textarea").autosize();
					$this.find("input[type=url]").keyup();
				})

				$.each(klosterstandorte, function (key, value) {
					if (key == 0) {
						var fieldset = $this.find('fieldset:eq(1)');
					}
					else {
						var fieldset = $this.find('div.multiple:eq(0)').clone(true);
					}

					fieldset.find('label :input').each( function() {
						var name = $(this).attr('name');
						if ( typeof name === 'undefined' ) return;
						name = name.replace('[]', '');
						var val = value[name];
						if ( $(this).is('[type=checkbox]') ) {
							if (name == "wuestung" && val==1) {
								$(this).prop('checked', true);
							}
							else {
								return;
							}
						} else if ( $(this).is('select') ) {

							if (name== "ort") {
								$this.find("select[name='ort[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}
							else if (name== "bistum") {
								$this.find("select[name='bistum[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}
							else if (name== "klosterstandort_zeitraum") {
								$this.find("select[name='klosterstandort_zeitraum[]'] option").each(function( i, opt ) {
									if( opt.value == val ) {
										$(opt).attr('selected', 'selected');
									}
								});
							}

						} else {
							$(this).val( val );
						}
					});

					if (key > 0) {
						$this.find('div.multiple:eq(0)').append(fieldset);
					}
					$this.find("textarea").autosize();
					$this.find("input[type=url]").keyup();
					})

			$.each(klosterurl, function (key, value) {
				$.each(value, function (key1, value1) {
					if (key1 == "GND")  { var gnd = value1; }
					else if (key1 == "Wikipedia") { var wikipedia=value1; }

					if (gnd) {
						//$("input[name=gnd]").val("");
						$("input[name=gnd]").val(gnd);
					}
					if (wikipedia) {
						//$("input[name=wikipedia]").val("");
						$("input[name=wikipedia]").val(wikipedia);
					}

				})
			})

			if (klosterliteratur && klosterliteratur != "") {
				$.each(klosterliteratur, function (key, value) {
					if($.isArray(value)) {
						$.each(value, function (literaturkey, literaturvalue) {
							$this.find("select[name='literatur[]'] option").each(function( i, opt ) {
							    if( opt.value == literaturvalue ) {
							        $(opt).attr('selected', 'selected');
							    }
							});
							})
						}
						else {
							$this.find("select[name='literatur[]'] option").each(function( i, opt ) {
								if( opt.value == value ) {
									$(opt).attr('selected', 'selected');
								}
							});
						}
				})
			}

		});
	},

// Der bearbeitete Eintrag aktualisieren
	update_kloster: function(url) {
		$.post(url, $("#EditKloster").serialize())
		.done(function(respond, status, jqXHR) {
			if (status == "success") {
				alert('Der Eintrag wurde erfolgreich bearbeitet.')
			}
		})
		.fail(function (jqXHR, textStatus) {
	        alert(jqXHR.responseText);
	   });
	},

// Das Formular zum Eintragen mit den Stammdaten ausfüllen
	new_kloster: function() {
		var url = "Subugoe.GermaniaSacra/kloster/new";
		$.getJSON(url, function(response){


			var ortGemeindeKreisArray = response[0];
			var $inputOrtGemeindeKreis = $("select[name='new_ort[]']");
			$.each(ortGemeindeKreisArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputOrtGemeindeKreis.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bearbeitungsstatusArray = response[1];
			var $inputBearbeitungsstatus = $("select[name='new_bearbeitungsstatus']");
			$inputBearbeitungsstatus.empty();
			$.each(bearbeitungsstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBearbeitungsstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var personallistenstatusArray = response[2];
			var $inputPersonallistenstatus = $("select[name='new_personallistenstatus']");
			$inputPersonallistenstatus.empty();
			$.each(personallistenstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputPersonallistenstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bandArray = response[3];
			var $inputBand = $("select[name='new_band']");
			$inputBand.empty();
			$inputBand.append($("<option>", { value: '', html: 'Kein Band' }));
			$.each(bandArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBand.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var literaturArray = response[4];
			var $inputLiteratur = $("select[name='literatur[]']");
			$inputLiteratur.empty();
			$.each(literaturArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputLiteratur.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var bistumArray = response[5];
			var $inputBistum = $("select[name='new_bistum[]']");
			$inputBistum.empty();
			$.each(bistumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputBistum.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var ordenArray = response[6];
			var $inputOrden = $("select[name='new_orden[]']");
			$inputOrden.empty();
			$.each(ordenArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputOrden.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var klosterstatusArray = response[7];
			var $inputKlosterstatus = $("select[name='new_klosterstatus[]']");
			$inputKlosterstatus.empty();
			$.each(klosterstatusArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputKlosterstatus.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var zeitraumArray = response[8];
			var $inputZeitraum = $("select[name='new_orden_zeitraum[]']");
			$inputZeitraum.empty();
			$.each(zeitraumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputZeitraum.append($("<option>", { value: v1, html: k1 }));
				});
			});

			var zeitraumArray = response[8];
			var $inputZeitraum = $("select[name='new_klosterstandort_zeitraum[]']");
			$inputZeitraum.empty();
			$.each(zeitraumArray, function (k, v) {
				$.each(v, function (k1, v1) {
					$inputZeitraum.append($("<option>", { value: v1, html: k1 }));
				});
			});


		});
	},

	create_kloster: function() {
		var url = "Subugoe.GermaniaSacra/kloster/create";
		$.post(url, $("#NewKloster").serialize())
		.done(function(respond, status, jqXHR) {
			if (status == "success") {
				alert('Der Eintrag wurde erfolgreich gespeichert.')
			}
		})
		.fail(function (jqXHR, textStatus) {
	        alert(jqXHR.responseText);
	   });

	}

});
