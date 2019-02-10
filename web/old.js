if (typeof Promise !== "undefined" && Promise.toString().indexOf("[native code]") !== -1) {
  console.log('Promise it is supported!');
} else {
  $("#form").slideUp();
  $("#msjNotSuport").slideDown();
  alert("In order to access and register for the IAP annual conference it is recommended the use of google chrome, Microsoft edge or firefox. Your current browser it’s not supported.");
}

var PAYMENT_GATEWAY_URL = 'https://betaling.wannafind.dk/paymentwindow/';
var ACCEPT_URL = 'http://52.44.147.225:7070/api/partaker/payments/confirm';
var DECLINE_URL = 'http://52.44.147.225:7070/api/partaker/payments/confirm';
var CONTRY_URL = 'http://52.44.147.225:7070/api/country';

var EARLY_BIRD_DATE = new Date(2018, 06, 15);
var TODAY = new Date();

var updatePrice = (TODAY.getTime() >= EARLY_BIRD_DATE.getTime());

var DAY_PARTICIPATION = Number(250.00);
var FULL_PARTICIPATION = updatePrice ?
  Number(950.00) :
  Number(950.00);
var ACCOMPANYING_PRICE = updatePrice ?
  Number(475.00) :
  Number(475.00);

var EARLY_BIRD_MSJ = updatePrice ?
  "" :
  "* EARLY BIRD BUY *";

var isSubtracted = false;

var Participant = {
  'grantingParticipant': false,
  'attend': false,
  'attendType': "",
  'contribute': false,
  'contributeType': "",
  'social': {
    'president': false,
    'accompanyingPresident': false,
    'conferenceDinner': false,
    'accompanyingConferenceDinner': false,
    'farewellParty': false,
    'accompanyingFarewellParty': false,
    'networkingNight': false
  },
  'requests': {
    'letterInvitation': false,
    'certificateAttendance': false
  },
  'accompanying': false,
  'companion': {},
  'committeeMember': false,
  'arrivalDetails': {}
};

function initializeRegisterPage() {
  getCountries();
  onClickTicketPlans();
  onClickTicketDays();
  onClickJobTitle();
  onClickContribute();
  onClickGrantingParticipant();
  onSelectCountry();
  onSelectContributeType();
  onClickAttend();
  onSelectAttendType();
  onClickAccompanyin();
  onClickAccompanyingToursDay();
  onClickSocialPresident();
  onClickConferenceDinner();
  onClickNetworkingNight();
  onClickFarewellParty();
  onClickLetterInvitation();
  onClickCertificateAttendance();
  onSelectPaymentType();
  onClickCommitteeMember();
  onBlurInputDateTime();
  onBlurInputText();
  //setEarlyBirdMsj();
}

function getCountries() {
  $.get(CONTRY_URL, function (data) {
    $.each(data, function (i, contry) {
      $('#country').append('<option value="' + contry.name + '">' + contry.name + '</option>');
    });
  }).fail(function () {
    swal("Oh noes!", "Can't load the countries list.", "error");
  });
}

function setEarlyBirdMsj() {
  $("#ebMsj").text(EARLY_BIRD_MSJ);
}

function updateTotalAmountInfo() {

  var spams = $("spam[class^='totalAmountInfo']");
  spams.each(function () {
    $(this).text(Participant.ticket.total);
  });

}

function onClickTicketPlans() {
  var ticketPlans = $('.pricing-plan');

  ticketPlans.each(function () {
    $(this).on('click', function () {

      ticketPlans.removeClass('active');
      $(this).addClass('active');

      var planId;

      var Plan_Value = Number($(this).find('.value').text()),
        Plan_Currency = $(this).find('.currency').text(),
        Plan_Title = $(this).find('h1, h2, h3, h4, h5, h6').text(),
        Plan_Description = $(this).find('.short-description').text();

      if (Plan_Description === "One Day access.") {

        console.log("One Day access.")

        $('#daysFieldSet').slideDown();
        $('#socialPresidentFieldset').slideUp();
        $('#conferenceDinnerFieldset').slideDown();
        $('#networkingNightFieldset').slideDown();
        $('#farewellPartyFieldset').slideDown();
        $('#textSelectedDays').slideDown();

        $('#accompanyingMsj').slideDown();
        $('#accompanyingContent').slideUp();
        planId = 1;

      } else {
        $('#daysFieldSet').slideUp();
        $('#socialPresidentFieldset').slideDown();
        $('#conferenceDinnerFieldset').slideDown();
        $('#networkingNightFieldset').slideDown();
        $('#farewellPartyFieldset').slideDown();
        $('#textSelectedDays').slideUp();

        $('#accompanyingMsj').slideUp();
        $('#accompanyingContent').slideDown();

        planId = 2;
      }

      Participant.ticket = {
        'id': planId,
        'description': Plan_Title,
        'value': Number(Plan_Value),
        'total': Number(Plan_Value)
      };

      $('#participationOptionInfo').text('"' + Participant.ticket.description + '"');
      updateTotalAmountInfo();
      $('#totalPayContainer').slideDown();

      if (Participant.ticket.id === 2) {
        navigateTab('ticket-tab', 'personal-tab');
      }

      //console.log(Participant);

    });
  });
}

function onClickTicketDays() {

  var ticketDays = $('input[name="days"]');
  ticketDays.each(function () {
    $(this).on('click', function () {
      if (!ticketDays.attr('hidden')) {

        $('#socialPresidentFieldset').slideUp();
        $('#conferenceDinnerFieldset').slideUp();
        $('#networkingNightFieldset').slideUp();
        $('#farewellPartyFieldset').slideUp();
        $('#listSelectedDays').empty();

        var days = [];

        $('input[name="days"]:checked').each(function () {
          var selecetedDay = $(this).val();
          days.push(selecetedDay);
          $('#listSelectedDays').append('<li>' + selecetedDay + '</li>');
          switch (selecetedDay) {
            case '09/10/2018':
              $('#conferenceDinnerFieldset').slideDown();
              break;
            case '09/11/2018':
              $('#networkingNightFieldset').slideDown();
              break;
            case '09/13/2018':
              $('#farewellPartyFieldset').slideDown();
              break;
          }

        });
        Participant.ticket.days = days;
        Participant.ticket.total = Number((Participant.ticket.value * days.length));
        updateTotalAmountInfo();
        //console.log(Participant);
      }
    });
  });

}

function onClickJobTitle() {
  var jobTitle = $('select[name=jobTitle]');
  jobTitle.change(function () {
    Participant.jobTitle = $(this).val();
    if (Participant.jobTitle == "Other") {
      $('#jobTitleOtherFieldset').slideDown();
    } else {
      $('#jobTitleOtherFieldset').slideUp();
      delete Participant.jobTitleOther;
    }
    //console.log(Participant);
  });
}

function onClickContribute() {
  var contribute = $('input[name="contribute"]');
  contribute.each(function () {
    $(this).on('click', function () {

      $('input[name="contribute"]:checked').each(function () {
        Participant.contribute = $(this).val();
        if (Participant.contribute == 'true') {
          $('#contributeTypeFieldset').slideDown();
        } else {
          $('#contributeTypeFieldset').slideUp();
          delete Participant.contributeType;
        }
      });
      //console.log(Participant);

    });
  });
}

function onClickGrantingParticipant() {
  var grantingParticipant = $('input[name="grantingParticipant"]');
  grantingParticipant.each(function () {
    $(this).on('click', function () {
      $('input[name="grantingParticipant"]:checked').each(function () {
        Participant.grantingParticipant = $(this).val();
      });
      //console.log(Participant);
    });
  });
}

function onSelectCountry() {
  var country = $('select[name=country]');
  country.change(function () {
    Participant.country = $(this).val();
    //console.log(Participant);
  });
}

function onSelectContributeType() {
  var contributeType = $('select[name=contributeType]');
  contributeType.change(function () {
    Participant.contributeType = $(this).val();
    //console.log(Participant);
  });
}

function onClickAttend() {
  var contribute = $('input[name="attend"]');
  contribute.each(function () {
    $(this).on('click', function () {

      $('input[name="attend"]:checked').each(function () {
        Participant.attend = $(this).val();
        /*if (Participant.attend == 'true') {
          $('#attendTypeFieldset').slideDown();
        } else {
          $('#attendTypeFieldset').slideUp();
          delete Participant.attendType;
        }*/
      });
      //console.log(Participant);

    });
  });
}

function onSelectAttendType() {
  var attendType = $('select[name=attendType]');
  attendType.change(function () {
    Participant.attendType = $(this).val();
    if (Participant.attendType == 'Executive Committee Member (substitute)' || Participant.attendType == 'IAP Officials/Staff') {
      $('#committeeMemberDiv').slideDown();
    } else {
      $('#committeeMemberDiv').slideUp();
      delete Participant.contributeType;
    }
    //console.log(Participant);
  });
}

function onClickAccompanyin() {
  var accompanyings = $('input[name="accompanying"]');
  accompanyings.each(function () {
    $(this).on('click', function () {

      $('input[name="accompanying"]:checked').each(function () {
        Participant.accompanying = $(this).val();
        if (Participant.accompanying == 'true') {
          $('#accompanyingDiv').slideDown();
          $('#textAccompanyingPerson').slideDown();
          $('#listSelectedDays').slideDown();
          Participant.ticket.total = Participant.ticket.value + ACCOMPANYING_PRICE;
          isSubtracted = false;
        } else if (!isSubtracted) {
          Participant.ticket.total = Participant.ticket.total - ACCOMPANYING_PRICE;
          $('#accompanyingDiv').slideUp();
          $('#textAccompanyingPerson').slideUp();
          $('#listSelectedDays').slideUp();
          delete Participant.companion;
          isSubtracted = true;
        }
      });

      $('input[class="socialEvent"][value=false]').prop('checked', true);
      Participant.social = {
        'president': false,
        'accompanyingPresident': false,
        'conferenceDinner': false,
        'accompanyingConferenceDinner': false,
        'farewellParty': false,
        'accompanyingFarewellParty': false,
        'networkingNight': false
      }

      updateTotalAmountInfo();
      //console.log(Participant);

    });
  });
}

function onClickAccompanyingToursDay() {

  var accompanyingToursDay = $('input[name="accompanyingToursDay"]');
  accompanyingToursDay.each(function () {
    $(this).on('click', function () {

      if (!Participant.companion) {
        Participant.companion = []
      }
      var days = [];

      $('input[name="accompanyingToursDay"]:checked').each(function () {
        var selecetedDay = $(this).val();
        days.push(selecetedDay);
      });

      Participant.companion.accompanyingToursDay = days;
      //console.log(Participant);
    });
  });

}

function onClickSocialPresident() {
  var socialPresident = $('input[name="socialPresident"]');
  socialPresident.each(function () {
    $(this).on('click', function () {

      $('input[name="socialPresident"]:checked').each(function () {
        Participant.social.president = $(this).val();
        if (Participant.accompanying === 'true' && Participant.social.president === 'true') {
          onClickSocialEventWithAccompanying('accompanyingPresident');
        } else {
          Participant.social.accompanyingPresident = false;
          console.log(Participant);
        }
      });
      //console.log(Participant);

    });
  });
}

function onClickConferenceDinner() {
  var conferenceDinner = $('input[name="conferenceDinner"]');
  conferenceDinner.each(function () {
    $(this).on('click', function () {

      $('input[name="conferenceDinner"]:checked').each(function () {
        Participant.social.conferenceDinner = $(this).val();
        if (Participant.accompanying === 'true' && Participant.social.conferenceDinner === 'true') {
          onClickSocialEventWithAccompanying('accompanyingConferenceDinner');
        } else {
          Participant.social.accompanyingConferenceDinner = false;
          console.log(Participant);
        }
      });
      //console.log(Participant);

    });
  });
}

function onClickNetworkingNight() {
  var networkingNight = $('input[name="networkingNight"]');
  networkingNight.each(function () {
    $(this).on('click', function () {

      $('input[name="networkingNight"]:checked').each(function () {
        Participant.social.networkingNight = $(this).val();
        if (Participant.accompanying === 'true' && Participant.social.networkingNight === 'true') {
          swal("Important Information", "You cannot bring accompanying to this event.", "warning");
        }
      });
      //console.log(Participant);

    });
  });
}

function onClickFarewellParty() {
  var farewellParty = $('input[name="farewellParty"]');
  farewellParty.each(function () {
    $(this).on('click', function () {

      $('input[name="farewellParty"]:checked').each(function () {

        Participant.social.farewellParty = $(this).val();

        if (Participant.accompanying === 'true' && Participant.social.farewellParty === 'true') {
          onClickSocialEventWithAccompanying('accompanyingFarewellParty');
        } else {
          Participant.social.accompanyingFarewellParty = false;
          console.log(Participant);
        }

      });

    });
  });
}

function onClickSocialEventWithAccompanying(attributeName) {
  console.log(attributeName);
  swal({
    title: "Will you bring your accompanying Person for this event?",
    icon: "warning",
    buttons: {
      yes: {
        text: "Yes",
        value: true,
      },
      no: {
        text: "No",
        value: false,
      }
    }
  }).then(function (value) {
    Participant.social[attributeName] = value;
    console.log(Participant);
  });
}


function onClickLetterInvitation() {
  var letterInvitation = $('input[name="letterInvitation"]');
  letterInvitation.each(function () {
    $(this).on('click', function () {

      $('input[name="letterInvitation"]:checked').each(function () {
        Participant.requests.letterInvitation = $(this).val();
      });
      //console.log(Participant);

    });
  });
}

function onClickCertificateAttendance() {
  var certificateAttendance = $('input[name="certificateAttendance"]');
  certificateAttendance.each(function () {
    $(this).on('click', function () {

      $('input[name="certificateAttendance"]:checked').each(function () {
        Participant.requests.certificateAttendance = $(this).val();
      });
      //console.log(Participant);

    });
  });
}

function onSelectPaymentType() {
  var paymentType = $('select[name=paymentType]');
  paymentType.change(function () {
    Participant.paymentType = $(this).val();
    //console.log(Participant);
  });
}

function onClickCommitteeMember() {
  var committeeMember = $('input[name="committeeMember"]');
  committeeMember.each(function () {
    $(this).on('click', function () {

      $('input[name="committeeMember"]:checked').each(function () {
        Participant.committeeMember = $(this).val();
        if (Participant.committeeMember == 'true') {
          $('#arrivalDetailsDiv').slideDown();
        } else {
          $('#arrivalDetailsDiv').slideUp();
          delete Participant.arrivalDetails;
        }
      });
      //console.log(Participant);

    });
  });
}

function onBlurInputDateTime() {

  var inputsDate = $('input[type="date"]');
  inputsDate.on('blur', function () {
    var name = $(this).attr("name");
    if (name.indexOf('arrival') !== -1) {
      Participant.arrivalDetails[name] = $(this).val();
    } else {
      Participant[name] = $(this).val();
    }
  });

  var inputsTime = $('input[type="time"]');
  inputsTime.on('blur', function () {
    var name = $(this).attr("name");
    if (name.indexOf('arrival') !== -1) {
      Participant.arrivalDetails[name] = $(this).val();
    } else {
      Participant[name] = $(this).val();
    }
  });
}

function onBlurInputText() {

  var inputs = $('input[type="text"]');
  inputs.on('blur', function () {
    var name = $(this).attr("name");

    if (name.indexOf('accompanyin') !== -1) {
      Participant.companion[name] = $(this).val();
    } else if (name.indexOf('arrival') !== -1) {
      Participant.arrivalDetails[name] = $(this).val();
    } else if (name.indexOf('prefix') !== -1) {
      if ($(this).val()) {
        Participant[name] = $(this).val() + '.';
        $(this).val($(this).val() + '.');
      }
    } else {
      Participant[name] = $(this).val();
    }
    //console.log(Participant);
  });

  var inputsEmail = $('input[type="email"]');
  inputsEmail.on('blur', function () {
    var name = $(this).attr("name");
    Participant[name] = $(this).val();
    //console.log(Participant);
  });

  var inputsTel = $('input[type="tel"]');
  inputsTel.on('blur', function () {
    var name = $(this).attr("name");
    Participant[name] = $(this).val();
  });

}

function backTab(toTabId) {

  if ((toTabId === 'social-tab' || toTabId === 'accompanying-tab') && Participant.ticket.id === 1) {

    if (Participant.ticket.days.length === 1) {

      if (Participant.ticket.days[0] === '09/12/2018') {

        $('.tab').removeClass('active');
        $('.tabLink').removeClass('active');
        $("#event-tab").addClass('active');
        $("#event-tab-link").addClass('active');
        $(document).scrollTop($("#tabTop").offset().top);
        return;
      }

      if (toTabId === 'accompanying-tab') {

        $('.tab').removeClass('active');
        $('.tabLink').removeClass('active');
        $("#event-tab").addClass('active');
        $("#event-tab-link").addClass('active');
        $(document).scrollTop($("#tabTop").offset().top);
        return;
      }
    }

    if (toTabId === 'accompanying-tab') {

      $('.tab').removeClass('active');
      $('.tabLink').removeClass('active');
      $("#event-tab").addClass('active');
      $("#event-tab-link").addClass('active');
      $(document).scrollTop($("#tabTop").offset().top);
      return;
    }

    $('.tab').removeClass('active');
    $('.tabLink').removeClass('active');
    $("#" + toTabId).addClass('active');
    $("#" + toTabId + "-link").addClass('active');
    $(document).scrollTop($("#tabTop").offset().top);

  } else {

    $('.tab').removeClass('active');
    $('.tabLink').removeClass('active');
    $("#" + toTabId).addClass('active');
    $("#" + toTabId + "-link").addClass('active');
    $(document).scrollTop($("#tabTop").offset().top);

  }

}

function navigateTab(fromTabId, toTabId) {
  if (validate(fromTabId)) {

    if ((fromTabId === 'event-tab' && toTabId == 'accompanying-tab') && Participant.ticket.id === 1) {

      if (Participant.ticket.days.length == 1) {
        if (Participant.ticket.days[0] === '09/12/2018') {
          $('.tab').removeClass('active');
          $('.tabLink').removeClass('active');
          $("#requests-tab").addClass('active');
          $("#requests-tab-link").addClass('active');
          $(document).scrollTop($("#tabTop").offset().top);
          return;
        }
      }

      $('.tab').removeClass('active');
      $('.tabLink').removeClass('active');
      $("#social-tab").addClass('active');
      $("#social-tab-link").addClass('active');

    } else {
      $('.tab').removeClass('active');
      $('.tabLink').removeClass('active');
      $("#" + toTabId).addClass('active');
      $("#" + toTabId + "-link").addClass('active');
    }

  }
  $(document).scrollTop($("#tabTop").offset().top);
}

function onFileChange(event) {
  var target = event.target || event.srcElement;
  var files = target.files;
  if (files.length > 0) {
    var fileInfo = files[0];
    this.handleImageLoad(fileInfo);
  }
}

function handleImageLoad(fileInfo) {

  //console.log(fileInfo.type);

  if (fileInfo.type != 'image/png' && fileInfo.type != 'image/jpeg' && fileInfo.type != 'image/jpg') {
    swal("Bad Format!", "Only accept image with .jpeg or .png format!", "warning");
    $("#inputImage").val('');
    return;
  }

  if (fileInfo.size > 2000000) {
    swal("Max size exceeded!", "The image size its to big, max size 2000 kilobytes!", "warning");
    $("#inputImage").val('');
    return;
  }

  var readerAsDataURL = new FileReader();
  readerAsDataURL.onloadend = function preview(e) {
    $('#profileImage').attr('src', readerAsDataURL.result);
    Participant.image = readerAsDataURL.result.split(',').pop();
  };
  readerAsDataURL.readAsDataURL(fileInfo);

}

function register() {
  console.log(Participant);
  if (validateJson()) {
    submitRegisterRequest();
  }
}

function validateJson() {

  if (Participant.firstName === '' || Participant.lastName === '' ||
    Participant.email === '' || Participant.mobilePhone === '') {
    swal("Bad Register State!", "Dear Customer, It is impossible to execute your register because the information is wrong. \\nPlease try again.\\nRemember: Do not must press the Back Button of your browser, this option cause problems in the register process.\\nThanks", "warning");
    location.reload();
    return false;
  }

  return true;

}

function validate(fromTabId) {

  var error = false;
  var emailError = false;
  var requiredInputs = $('#' + fromTabId).find($(".required"));
  requiredInputs.removeClass('error');
  requiredInputs = requiredInputs.filter(function () {

    if ($(this).attr('name').indexOf('jobTitleOther') !== -1) {
      if ($('#jobTitleOtherFieldset').css('display') == 'none') {
        return false;
      }
    } else if ($(this).attr('name').indexOf('accompanying') !== -1) {
      if ($('#accompanyingDiv').css('display') == 'none') {
        return false;
      }
    } else if ($(this).attr('name').indexOf('arrival') !== -1) {
      if ($('#arrivalDetailsDiv').css('display') == 'none') {
        return false;
      }
    } else if ($(this).attr('name').indexOf('contribute') !== -1) {
      if ($('#contributeTypeFieldset').css('display') == 'none') {
        return false;
      }
    }
    /*else if ($(this).attr('name').indexOf('attend') !== -1) {
         if ($('#attendTypeFieldset').css('display') == 'none') {
           return false;
         }
       }*/

    return true;
  });

  requiredInputs.each(function () {

    if ($.trim($(this).val()) == '' || $.trim($(this).val()) == $.trim($(this).attr('placeholder'))) {
      $(this).addClass('error');
      error = true;
    }
    if ($(this).attr('name') === 'email') {
      var email = $(this).attr('type', 'email').val(),
        Pattern = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if (!Pattern.test(email)) {
        $(this).addClass('error');
        emailError = true;
      }
    }

  });

  if (!Participant.ticket) {
    swal("Required!", "Please, select a participation option!", "warning");
    return false;
  }

  if (Participant.ticket.id == 1) {
    if (!Participant.ticket.days) {
      swal("Required!", "Please, select  at least one attend date!", "warning");
      return false;
    }

    if (Participant.ticket.total == 0) {
      swal("Required!", "Please, select  at least one attend date!", "warning");
      return false;
    }

  }

  if (Participant.accompanying == true && !Participant.companion.accompanyingToursDay) {
    swal("Required!", "Please, select which Special Tours your Accompanying Person wishes to attend!", "warning");
    return false;
  }

  if (error) {
    swal("Required!", "Please, fill all required fields!", "warning");
    return false;
  }

  if (emailError) {
    swal("Required!", "Please, fill email is a required field, or it's has invalid format!", "warning");
    return false;
  }

  return true;

}

function submitRegisterRequest() {

  swal({
    'title': "Are you sure?",
    'text': 'Do you want to register for this conference?',
    'icon': "warning",
    'buttons': {
      'cancel': {
        text: "Cancel",
        value: null,
        visible: true,
        closeModal: true
      },
      'confirm': {
        text: "Register",
        value: true,
        visible: true,
        closeModal: false
      }
    },
    'dangerMode': true
  }).then(function (willCreate) {
    if (!willCreate) {
      throw null;
    }
    return this.requestSave();
  }).then(function (results) {
    return results.json();
  }).then(function (json) {
    //console.log(json);
    processResponse(json);
  }).catch(function (error) {
    //console.log("cath error = " + JSON.stringify(error));
    if (error) {
      if (error.entity) {
        swal("Oh noes!", 'The request failed, cause: ' + error.entity.error, "error");
      } else {
        swal("Oh noes!", "The request failed.", "error");
      }
    } else {
      swal.stopLoading();
      swal.close();
    }
  });

}
/**
 * NICO:
 * aca se hace el submit al backend
 * */
function requestSave() {
  //return fetch('http://52.44.147.225:7070/api/partaker', {//NICO: esta es la url original
  return fetch('/ajax/submit.php', {	  
    method: 'post',
    headers: {
      'Accept': 'application/json, text/plain, */*',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(Participant)
  });
}

function submitOnlyPaymentGateWay(code, total) {

  var form = $('<form></form>');
  form.attr("method", "post");
  form.attr("action", PAYMENT_GATEWAY_URL);

  var parameters = [{
    'name': 'shopid',
    'value': '200707241615'
  }, {
    'name': 'currency',
    'value': '978'
  }, {
    'name': 'amount',
    'value': total + '00'
  }, {
    'name': 'orderid',
    'value': Number(code)
  }, {
    'name': 'uniqueorderid',
    'value': true
  }, {
    'name': 'lang',
    'value': 'en'
  }, {
    'name': 'protocol',
    'value': '1'
  }, {
    'name': 'paytype',
    'value': 'creditcard'
  }, {
    'name': 'accepturl',
    'value': ACCEPT_URL
  }, {
    'name': 'declineurl',
    'value': DECLINE_URL
  }];

  $.each(parameters, function (i, obj) {
    var field = $('<input></input>');
    field.attr("type", "hidden");
    field.attr("name", obj.name);
    field.attr("value", obj.value);
    form.append(field);
  });

  $(document.body).append(form);
  form.submit();

}

/*
 * NICO:
 * aca hace el vnio al gateway d epagos, en caso q haya elegido pago con tarjeta
 */
function processResponse(json) {
	//console.log(json);
  if (json.hasOwnProperty('code')) {
    var code = json.code;
    var total = json.total;
    console.log(code+" "+total)//NICO:estos dos valores los devuelve el back, para enviarselo al gateway, y despues cuando el gateway responde con este codigo, saber a que usuario se acredito el pago 
    //submitOnlyPaymentGateWay(code, total);
  } else {
    var msj = json.response.message;
    swal("Registration Completed!", msj, "success").then(function () {
      window.location.href = 'index.html';
    });
  }
}

$(document).ready(function () {
  initializeRegisterPage();
  $('#dayParticipationPrice').text(DAY_PARTICIPATION);
  $('#fullParticipationPrice').text(FULL_PARTICIPATION);
  $('#accompanyingPrice').text(ACCOMPANYING_PRICE);
  $("#form")[0].reset();
  console.log('documento listo');
});

$(window).load(function () {
  $('body').backDetect(function () {
    alert("Dear Customer, Remember: Do not must press the Back Button of your browser, this option cause problems in the register process.!");
    location.reload();
  });
});
