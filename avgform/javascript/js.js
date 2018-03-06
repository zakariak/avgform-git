var formDataLookup = {};
var choiceHistory = [];
var submitHistory = {};
var questions = {};
var result = null;
// console.log(choiceHistory);
function getJson() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var obj = JSON.parse(this.responseText);
      onConfigurationReady(obj);

    }
  };
  xhttp.open("GET", "php/json.php", true);
  xhttp.send();
}


function onConfigurationReady(configuration) {

  var home = configuration.home;
  questions = configuration.questions;
  result = configuration.results;


  for(key in questions){
    var questionInfo = questions[key];
    formDataLookup[questionInfo.id] = questionInfo;
  }
  homepage(home);

  document.getElementById("start").addEventListener("click", function() {
    getQuestion(questions[0]);
  });

  document.getElementById("result").addEventListener("click", function(ev) {
        ev.preventDefault();
            var elTarget = document.getElementById('questionOutput');
            var elTarget2 = document.getElementById('form');
        removeAllChildren(elTarget);
        elTarget2.style.marginTop = '0';
        elTarget2.style.marginBottom = '0';

        validateInput();
        jsonToPhp()
        sumAll(questions);
        window.scrollTo(500, 0);
  });
}

getJson();


function sumAll(questionsParam) {
  total = 0;
  for (key in choiceHistory) {
    var choiceInfo = choiceHistory[key];
    questionByChoice = choiceHistory[key].q;
    answerByChoice = choiceHistory[key].a;
    points = parseInt(questionsParam[questionByChoice].options[answerByChoice].points);
    total += points
  }
  return total;
}


function jsonToPhp() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
    }
  }
  xhttp.open("POST", "php/formOutput.php", true);

var postData = {
  choiceHistory: choiceHistory,
  submitHistory: submitHistory
};

var postDataString = JSON.stringify(postData);

  xhttp.send(postDataString);
}

function arrayToJson(array) {
  var json = JSON.stringify(array);
  return json;
}

function validateInput() {

  var name =  document.getElementById('naam');
  var bname = document.getElementById('bedrijfsnaam');
  var email = document.getElementById('email');
  // console.log(bname.value);
  if (name.value == '' || bname.value == '' || email.value == '') {
    alert("wrong boi");
  } else {


    submitHistory.name = name.value;
    submitHistory.bname = bname.value;
    submitHistory.email = email.value;

    var eltarget2 = document.getElementById('questionOutput');
    var elTarget = document.getElementById('form');
    removeAllChildren(elTarget);
    removeAllChildren(eltarget2);
    createResult();
  }
}

function getQuestion(question) {
  var elTarget = document.getElementById('questionOutput');
  // empty the question-container
  removeAllChildren(elTarget);
  var submitFunction = null;

  if (question.id == 'Finish') {
    removeAllChildren(elTarget);
    document.getElementById("form").style.display = "block";
    elTarget.style.marginTop = '0px';
    elTarget.style.marginBottom = '0px';
  }
  else {
    var elContainer = document.createElement('div');
    elContainer.className = 'container-question';

    var elGridQuestion = document.createElement('div');
    elGridQuestion.setAttribute('class', 'col-12 col-m-12');

    var elQuestion = document.createElement('div');
    elQuestion.className = 'question center';
    elQuestion.innerText = question.desc;

    var elInfo = document.createElement('i');
    elInfo.setAttribute('class', 'fa fa-info');

    elInfo.addEventListener("click", function() {
      var info = document.getElementById('infoid');
      if (info.style.display === "none") {
        info.style.display = "block";
      } else if (info.style.display === "block") {
        info.style.display = "none";
      };
    });

    elQuestion.appendChild(elInfo);
    elGridQuestion.appendChild(elQuestion);

    var elRowQuestion = document.createElement('div');
    elRowQuestion.setAttribute('class', 'row');
    elRowQuestion.appendChild(elGridQuestion);

    elContainer.appendChild(elRowQuestion)

    var elGridAnswer = document.createElement('div');
    elGridAnswer.setAttribute('class', 'col-12 col-m-12');

    var elAnswer = document.createElement('p');
    elRowQuestion.setAttribute('class', 'answer');

    elGridAnswer.appendChild(elAnswer);

    if (question.type == "janee") {
      var name = null;
      for (key in question.options) {
        question.options[key].index = key;
        // create the label, radio buttons and info from the question
        var label = createLabel(question.options[key]);
        var radio = createRadio(question.options[key]);
        var info = createInfo(question);

        var elRowLabel = document.createElement('row');
        elRowLabel.setAttribute('class', 'row');

        var firstCol = document.createElement('div');
        firstCol.setAttribute('class', 'col-12 col-m-12');

        var elemP = document.createElement('p');

        var elRowInfo = document.createElement("div");
        elRowInfo.setAttribute("class", "row");

        var elGridInfo = document.createElement("div");
        elGridInfo.setAttribute("class", "col-12 col-m-12");

        elGridInfo.appendChild(info);
        elRowInfo.appendChild(elGridInfo);

        elemP.appendChild(label);
        elemP.appendChild(radio);
        firstCol.appendChild(elemP);
        elRowLabel.appendChild(firstCol);
        elAnswer.appendChild(elRowLabel);

        name = question.options[key].name;
      }

      submitFunction = function(e) {
        var allRadios = document.getElementsByName('helloworld');
        var SelectedRadio = null;
        for (var i = 0; i < allRadios.length; i++) {
          if (allRadios[i].checked) {
            SelectedRadio = allRadios[i];
            break;
          }
        }
        if (!SelectedRadio) {
          var elGridWarning = document.createElement('div');
          elGridWarning.setAttribute('class', 'col-12 col-m-12');

          var elRowWarning = document.createElement('div');
          elRowWarning.setAttribute('class', 'row')

          var elWarning = document.createElement('div');
          elWarning.setAttribute('id', 'warning');

          elRowWarning.appendChild(elGridWarning);
          elGridWarning.appendChild(elWarning);
          elContainer.appendChild(elRowWarning);

          elWarning.innerHTML = "Choice a option!";
        }
        else {
            getQuestion(formDataLookup[SelectedRadio.optionDetails.then]);
            choiceHistory.push({q: question.id, a:  SelectedRadio.optionDetails.index});
        }
      }
    }

    // else if (question.type == "singlechoice") {
    //   // var select = createSelect(question.options, question.id);
    //   // elAnswer.appendChild(select);
    //
    //   // HERE COMES THE CODE FOR A SECOND CHOICE
    // }

    elContainer.appendChild(elRowInfo);
    elContainer.appendChild(elAnswer);
    elTarget.appendChild(elContainer);

    var abutton = createButton(question.options[key], submitFunction);
    elContainer.appendChild(abutton);
  }
}

function homepage(home) {
  var title = document.getElementById("title");
  title.innerText = home.title;

  var subtitle = document.getElementById("subtitle");
  subtitle.innerText = home.subtitle

  var content = document.getElementById("content");
  content.innerHTML  = home.contentparaf1 + "<br>" + home.contentparaf2 + "<br>" + home.contentparaf3 + "<br>" + home.contentparaf4 + "<br>" + home.contentparaf5;
}

function removeAllChildren(htmlElement) {
  while (htmlElement.hasChildNodes()) {
    htmlElement.removeChild(htmlElement.firstChild);
  }
}

function createInfo(options) {
  var elInfo = document.createElement("div");
  elInfo.setAttribute("class", "info-question");
  elInfo.setAttribute('id', 'infoid')
  elInfo.innerText = options.uitleg;

  elInfo.style.display = "none";
  elInfo.style.fontStyle = "italic"

  return elInfo;
}

function createButton(options, fn) {
  var button = document.createElement('button');
  button.setAttribute('class', 'questionButton');
  button.innerText = 'Next question';
  button.optionDetails = options;

  if (fn) {
    button.addEventListener("click", fn);
  }
  return button;
}

function createRadio(options) {
  var radioSelection = document.createElement('input');
  radioSelection.setAttribute('type', 'radio');
  radioSelection.setAttribute('name', 'helloworld');
  radioSelection.optionDetails = options;
  return radioSelection;
}

for (key in formDataLookup) {
  var a = formDataLookup[key];

  continue;
}

function createSelect(options, questionId) {
  var select = document.createElement('select');
  for (key in options) {
    var optionSelection = document.createElement('option');
    optionSelection.setAttribute('data-key', key);
    optionSelection.setAttribute('data-questionId', questionId);
    optionSelection.optionDetails = options[key];
    optionSelection.addEventListener("click", function(e) {
    });

    optionSelection.setAttribute('name', options[key].name);
    optionSelection.setAttribute('value', options[key].value);
    optionSelection.setAttribute('data-then', options[key].then);
    optionSelection.innerText = options[key].desc;
    select.appendChild(optionSelection);
  }
  return select;
}

function createLabel(options) {
  var label = document.createElement('label');
  label.innerText = options.desc;
  return label;
}

function createResult() {
  var resultPage = document.getElementById('resultPage');

  var resultContainer = document.createElement("p");
  resultContainer.setAttribute('id', 'resultContainer');

  var resultTitle = document.createElement('div');
  resultTitle.setAttribute('id', 'resultTitle');
  resultTitle.innerText = "Resultaat: ";

  resultPage.appendChild(resultTitle);
  resultPage.appendChild(resultContainer);

  var resultdescContainer = document.createElement("p");
  resultdescContainer.setAttribute('id', 'resultDescContainer');

  var resultDescTitle = document.createElement('p');

  resultPage.appendChild(resultDescTitle);
  resultPage.appendChild(resultdescContainer);

  var resultlinkContainer = document.createElement("p");
  resultlinkContainer.setAttribute('id', 'resultLinkContainer');
  resultPage.appendChild(resultlinkContainer);

  var total = sumAll(questions);
  for (key in result) {
    if ((total >= result[key].mintotalpoints) && (total <= result[key].maxtotalpoints)) {
      resultContainer.innerText = result[key].name;
      resultdescContainer.innerText = result[key].resultdesc;
      resultlinkContainer.innerText = result[key].link;
      break;
    }
  }
  resultPage.style.display = "block";
}
