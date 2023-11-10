//get first element using css selector
const getElement = selector => {
  return document.querySelector(selector);
}
//get/set value
const getVal = sel => getElement(sel).value
const setVal = (sel,value) => getElement(sel).value = value
//get/set innerHTML
const getHtml = sel => getElement(sel).innerHTML
const setHtml = (sel,value) => getElement(sel).innerHTML = value
//clear value/innerHTML
const clear = (sel) => {setVal(sel,""); setHtml(sel,"");}
//add/remove class
const addClass = (sel,className) => getElement(sel).classList.add(className)
const removeClass = (sel,className) => getElement(sel).classList.remove(className)
// set/remove attributes
const setAttr = (sel, attr, value) => getElement(sel).setAttribute(attr, value)
const removeAttr = (sel, attr) => getElement(sel).removeAttribute(attr)

const filterTable = (inputId,tableId) => {
    // Declare variables
    var filter, tr, td, i, txtValue
    filter = getVal(inputId).toUpperCase()
    tr = getElement(tableId).getElementsByTagName("tr")
  
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")
      let matched = false
      for(count = 0; count < td.length; count++){
        if (td[count]) {
            txtValue = td[count].textContent || td[count].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              matched = true
              continue
            }
        }
      }
      if(!matched){
        tr[i].style.display = "none";
      }else{
        tr[i].style.display = "";
      }
    }
}

const filterDrop = (input,divId) => {
  let filter = input.value.toUpperCase()
  let options = getElement(divId).getElementsByTagName("div")
  let i = 0
  for(i=0; i<options.length;i++){
    let txt = getHtml(options[i])
    if(txt.toUpperCase().indexOf(filter) > -1){
      options[i].style.display = ""
    }else{
      options[i].style.display = "none"
    }
  }
}
//onfirmation
const confirmation = message => confirm(message)
//toggle password input
const showPassword = sel => {
  let input = getElement(sel)
  let type = input.getAttribute("type")
  if(type == "text"){
    input.setAttribute("type","password")
    return
  }    
  input.setAttribute("type","text")
}
const passText = (origin,destination) => {
  let txt =  getHtml(origin)
  setVal(destination,txt)
}

// const copyToClipboard = (text) => {
//   navigator.clipboard.writeText(text).then(function() {
//     alert('Text copied to clipboard');
//   }).catch(function(err) {
//     console.error('Failed to copy text: ', err);
//   });
// }

// UPDATED CLIPBOARD CODE
const copyToClipboard = (text) => {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text)
      .then(() => alert('Text copied to clipboard'))
      .catch((err) => console.error('Failed to copy text: ', err));
  } else {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    if (isMobile) {
      // For mobile devices
      const textArea = document.createElement("textarea");
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();

      try {
        document.execCommand('copy');
        alert('Text copied to clipboard');
      } catch (err) {
        console.error('Failed to copy text: ', err);
      } finally {
        document.body.removeChild(textArea);
      }
    } else {
      alert('Clipboard not supported on this device');
    }
  }
};

// check if number
const validateNumberInput = (a) => {
  a.value = a.value.replace(/[^0-9]/g, '');
}


//HTMX ERROR HANDLER
const htmxError = (formId,errContainerId) => {
  getElement(formId).addEventListener('htmx:responseError', function(evt) {
  getElement(errContainerId).innerHTML = evt.detail.xhr.response
  });
}

const htmxSuccess = (formId,customFunction) => {
  getElement(formId).addEventListener('htmx:afterOnLoad', function(evt) {
    if(!evt.detail.failed)
    customFunction()
  });
}