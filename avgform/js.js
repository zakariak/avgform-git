var formData = [
  {
    id: "A",
    desc: "Heb je ook zo'n honger?",
    type: "janee",
    options: [
      {
        desc: "Ja",
        value: -20,
        then: "B",
      },
      {
        desc: "Nee",
        value: 7,
        then: "A",
      },
    ]
  },
  {
    id: "B",
    desc: "Lievelingskleur",
    type: "singlechoice",
    options: [
      {
        desc: "rood",
        value: 3,
        then: "A",
      },
      {
        desc: "blauw",
        value: 23,
        then: "A",
      },
      {
        desc: "ik ben hier klaar mee",
        value: 43,
        then: null,
      },
    ]
  },
  {
    id: "C",
    desc: "Haat jij het ook?",
    type: "janee",
    options: [
      {
        name: "Haat",
        desc: "Ja",
        value: 10,
        then: "E",
      },
      {
        name: "Haat",
        desc: "Nee",
        value: -10,
        then: "D",
      }

    ]
  }
];




// var formDataLookup = {};

var currentQuestion = formData[0].desc;
document.getElementsByClassName('question').innerHTML = currentQuestion;

function createRadioButtons(options, type){

    var optionSelection = document.createElement('input');
    optionSelection.setAttribute('type', 'radio');
    optionSelection.setAttribute('name', type);

  return optionSelection
}

function createSelect(options, select, type) {

        var optionSelection = document.createElement('option');
        optionSelection.setAttribute('name', options);

        var select = document.createElement('select');
        select.setAttribute('name', select);

        return (optionSelection, select);
}

function optionList(question, type) {
  var elTarget = document.getElementById('questionOutput');

  var elContainer = document.createElement('div');
  elContainer.className = 'container';
  elTarget.appendChild(elContainer);

  var elQuestion = document.createElement('span');
  elQuestion.className = 'question';
  elContainer.appendChild(elQuestion);

  var elAnswers = document.createElement('div');
  elAnswers.className = 'answers';

  elQuestion.innerText = question.desc;

  if (type == "janee") {
    for(options in question.options){
      var node = createRadio(question.options[options], question.type);
      console.log(node);    
      elAnswers.appendChild(node);
    }
  }

  else if (type = "singlechoice") {
    for(options in question.options){
      var node = createSelect(question.options[options], question.type);
      // console.log(node);
      elAnswers.appendChild(node);
    }

  }


      elTarget.appendChild(elAnswers);
}
optionList(formData[0], formData);
optionList(formData[1]);
