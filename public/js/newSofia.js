/////////////////////////////////////////////////////////////////////
// Variables
/////////////////////////////////////////////////////////////////////
const chat__body = document.querySelector(".chat__body");
const chat__send = document.getElementById("chat__send");
const chat__textarea = document.getElementById("chat__textarea");
const chat__toggle = document.getElementById("chat__toggle");
const chat__close = document.getElementById("chat__close");
const chat__sideopen = document.getElementById("chat__sideopen");
const chat__sidebar = document.getElementById("chat__sidebar");
const chat__sideclose = document.getElementById("chat__sideclose");
const chat__sideopen__mobile = document.getElementById(
  "chat__sideopen__mobile"
);
const chat__search = document.getElementById("chat__search");
const chat__searchform = document.getElementById("chat__searchform");
const chat__sidecontent = document.getElementById("chat__sidecontent");
const sofiaChat = document.getElementById("sofia-chat");

const ticketemail = document.getElementById("ticketemail");
const isassistenza = document.getElementById("isassistenza");
const ordiniselect = document.getElementById("ordiniselect");
const messaggioticket = document.getElementById("messaggioticket");
const sendticket = document.querySelector("[data-sendticket]");

/////////////////////////////////////////////////////////////////////
// Imperative code
/////////////////////////////////////////////////////////////////////

let sendToSofia = InviaMessaggio;
let stopChat = false;
let updateInterval;
let needInitialize = true;
let initialHeight;

sofiaChat.style.display = "none";
init();

/////////////////////////////////////////////////////////////////////
// Events listeners
/////////////////////////////////////////////////////////////////////

chat__sideclose.onclick = () => chat__sideopen.click();

chat__sideopen__mobile.onclick = () => chat__sideopen.click();

chat__toggle.onclick = () => sofiaChat.classList.remove("closed");

chat__close.onclick = () => {
  sofiaChat.classList.add("closed");
  if (chat__sidebar.classList.contains("open")) chat__sideopen.click();
};

chat__send.onclick = () => InviaMessaggio();

/*chat__textarea.oninput = () => {
  const messaggio = chat__textarea.value.trim();
  if (messaggio == "") chat__send.setAttribute("disabled", true);
  else chat__send.removeAttribute("disabled");
  AdjustTextArea();
};*/

window.addEventListener("mousedown", () => {
  sofiaChat.classList.add("closed");
  if (chat__sidebar.classList.contains("open")) chat__sideopen.click();
});

sofiaChat.addEventListener("mousedown", (e) => {
  e.stopPropagation();
});

chat__toggle.addEventListener("click", (e) => e.stopPropagation());
/*
chat__textarea.onkeydown = (e) => {
  if (e.key == "Enter" && !e.shiftKey && chat__textarea.value.trim() != "")
    InviaMessaggio();
  if (!e.shiftKey && e.key == "Enter") e.preventDefault();
  AdjustTextArea();
};
*/
chat__sideopen.onclick = () => {
  chat__sideopen.querySelector("span").style.transform =
    chat__sidebar.classList.contains("open")
      ? "rotateY(-180deg)"
      : "rotateY(0deg)";
  if (chat__sidebar.classList.contains("open")) CloseAll();
  chat__sidebar.classList.toggle("open");
};

chat__searchform.addEventListener("submit", (e) => {
  e.preventDefault();
  SearchProduct(chat__search.value);
});
chat__searchform.addEventListener("change", (e) => {
  e.preventDefault();
  SearchProduct(chat__search.value);
});

isassistenza.onchange = async (e) => {
  if (e.target.checked) {
    const info = await getInfoCliente();
    const { logged, messaggio } = info;
    if (!logged) {
      alert(messaggio);
      e.target.checked = false;
      return;
    }
    document
      .querySelector("[data-ticketcontainer]")
      .setAttribute("data-showordini", true);
    return;
  }
  document
    .querySelector("[data-ticketcontainer]")
    .removeAttribute("data-showordini");
};

sendticket.addEventListener("click", handleTicketSubmission);

/////////////////////////////////////////////////////////////////////
// Funzioni
/////////////////////////////////////////////////////////////////////

function init() {
  setTimeout(() => {
    sofiaChat.style.display = "block";
  }, 500);
  if (OneHourPassed(localStorage.getItem("sofia__time")) || NotInLocalStorage()) CreateLocalStorage();
  let arr = Array.from(JSON.parse(localStorage.getItem("sofia__chat")));
  arr.forEach((el) => {
    if (el.msg == "" || el.user == null) return;
    AggiungiMessaggio(el.msg, el.user);
  });
  startChat();
  setTimeout(() => {
    ScrollBottom();
  }, 1000);
}

async function startChat () {
  const freeOp = await freeOperators();

  if (!InOrarioDiLavoro() || !freeOp) {
    AggiungiMessaggio(
      "Welcome!",
      false
    );
    return;
  }
  ConnettiConOperatore();

}


function AdjustTextArea() {
  if (needInitialize) {
    initialHeight = chat__textarea.scrollHeight;
    needInitialize = false;
  }
  chat__textarea.removeAttribute("style");
  if (chat__textarea.scrollHeight >= initialHeight * 2) {
    chat__textarea.style.height = initialHeight * 2 + "px";
    return;
  }
  chat__textarea.style.height = chat__textarea.scrollHeight + 0.3 + "px";
}

function InviaMessaggio() {
    var message_chat = document.getElementById("chat__textarea").value;
    var username_chat = $("#user_name").val();
    var user_chats = $("#user_chats").val();
    var keyy = 2;
   // console.log("Messaggio chat:", message_chat);
   // console.log("Username chat:", username_chat);
   // console.log("User chats:", user_chats);

    // Esegui una richiesta AJAX per inviare il messaggio al server
    $.ajax({
        url: 'https://kloe.zenchat.it/ajax/sendMessage.php', 
        method: 'POST',
        data: { message_chat: message_chat, username_chat: username_chat, user_chats: user_chats, keyy: keyy },
        success: function () {
            $("#chat__textarea").val('');
        }
    });

}


function AggiungiMessaggio(msg, user = false) {
  const sofiamsg = document.createElement("div");
  sofiamsg.className = user ? "user-msg" : "sofia-msg";
  const content = document.createElement("p");
  content.className = "content";
  content.innerHTML = msg;
  sofiamsg.appendChild(content);
  chat__body.appendChild(sofiamsg);
  scrollLastMessageIntoView();
}

function scrollLastMessageIntoView() {
  chat__body.querySelector("div:last-child").scrollIntoView();
}



function SalvaMessaggio(msg, user) {
  if (NotInLocalStorage() || OneHourPassed(localStorage.getItem("sofia__time")))
    CreateLocalStorage();
  let arr = Array.from(JSON.parse(localStorage.getItem("sofia__chat")));
  arr.push({ msg, user });
  localStorage.setItem("sofia__chat", JSON.stringify(arr));
  localStorage.setItem("sofia__time", new Date().toISOString());
}

function NotInLocalStorage() {
  if (
    localStorage.getItem("sofia__chat") == null ||
    localStorage.getItem("sofia__time") == null
  ) {
    return true;
  }
  return false;
}

function CreateLocalStorage() {
  localStorage.setItem("sofia__chat", JSON.stringify([]));
  localStorage.setItem("sofia__time", new Date().toISOString());
}

function OneHourPassed(data) {
  if (data == null) return true;
  return new Date() - new Date(data) > 3600 * 1000;
}

function ScrollBottom() {
  chat__body.scrollTop = chat__body.scrollHeight;
}

function SearchProduct(msg) {
  CloseAll();
  if (sofiaChat.classList.contains("closed"))
    sofiaChat.classList.remove("closed");
  if (!chat__sidebar.classList.contains("open")) chat__sideopen.click();
  //const info = await getInfoCliente();
  const { logged, messaggio, email, nome, cognome, ordini } = info;
  if (logged) {
    ticketemail.value = email;
    ticketemail.disabled = true;
    ordiniselect.innerHTML = "";
    [...ordini].forEach((ordine) => {
      ordiniselect.innerHTML += `
        <option value="${ordine.SO}" data-idordine="${ordine.idOrder}" data-nome="${ordine.NomeProdotto}"> ${ordine.NomeProdotto} </option>
      `;
    });

    // Aggiungi il form per l'inserimento di un ticket
    const ticketForm = document.createElement("form");
    ticketForm.id = "ticketForm";

    const emailInput = document.createElement("input");
    emailInput.type = "email";
    emailInput.id = "ticketemail";
    emailInput.value = email;
    emailInput.disabled = true;

    const orderSelect = document.createElement("select");
    orderSelect.id = "ordiniselect";
    // Aggiungi le opzioni per gli ordini qui...

    const messageTextArea = document.createElement("textarea");
    messageTextArea.id = "messaggioticket";

    const sendTicketButton = document.createElement("button");
    sendTicketButton.textContent = "Invia Ticket";

    ticketForm.appendChild(emailInput);
    ticketForm.appendChild(orderSelect);
    ticketForm.appendChild(messageTextArea);
    ticketForm.appendChild(sendTicketButton);

    chat__sidebar.appendChild(ticketForm);

    ticketForm.addEventListener("submit", (e) => {
      e.preventDefault();
      handleTicketSubmission();
    });
  } else {
    alert("Per vedere i tuoi ordini devi aver effettuato l'accesso");
  }
}

function InserisciProdottiCercati(prodotti) {
  document.querySelector(".chat__placeholder").style.cssText =
    "display: none !important;";

  document.querySelector(".slideProdottiSofia ").style.display = "block";

  chat__sidecontent.querySelector(".swiper-wrapper").innerHTML = "";

  Array.from(prodotti).forEach((prodotto) => {
    const slide = document.createElement("div");
    slide.className = "swiper-slide";

    slide.innerHTML += `
    <div data-prodotto >
      <img src="${prodotto.img}">  
      <div>
      <p data-titolo>${prodotto.nome.substr(0, 30).trim()}...</p>
      <p data-prezzo>${prodotto.prezzo} €</p>
      <p data-disponibilita>${prodotto.disponibile}</p>
      <a href="${prodotto.link}" target="_blank" data-url>Vai al prodotto</a>
      </div>
    </div>

    `;

    chat__sidecontent.querySelector(".swiper-wrapper").append(slide);
  });

  var sliderSwiper = new Swiper(".slideProdottiSofia", {
    loop: true,
    pagination: {
      el: ".swiper-pagination",
    },
  });
}

async function getInfoCliente() {
  const res = await fetch("ajax/logged.php");
  const json = await res.json();
  return json;
}

async function OpenSendTicket() {
  CloseAll();
  if (sofiaChat.classList.contains("closed"))
    sofiaChat.classList.remove("closed");
  if (!chat__sidebar.classList.contains("open")) chat__sideopen.click();

  const info = await getInfoCliente();
  const { logged, messaggio, email, nome, cognome, ordini } = info;
  if (logged) {
    ticketemail.value = email;
    ticketemail.disabled = true;
    ordiniselect.innerHTML = "";
    [...ordini].forEach((ordine) => {
      ordiniselect.innerHTML += `
        <option value="${ordine.SO}" data-idordine="${ordine.idOrder}" data-nome="${ordine.NomeProdotto}"> ${ordine.NomeProdotto} </option>
      `;
    });
  }
  chat__sidebar.setAttribute("data-showticket", true);
}

function CloseSendTicket(svuota = true) {
  if (svuota) {
    ticketemail.value = "";
    ticketemail.disabled = false;
    ordiniselect.innerHTML = "";
    messaggioticket.value = "";
    isassistenza.checked = false;
  }

  chat__sidebar.removeAttribute("data-showticket", true);
}
async function handleTicketSubmission() {
  const info = await getInfoCliente();
  const { logged, messaggio, email, nome, cognome, ordini } = info;
  
  if (ticketemail.value.trim() == "" || messaggioticket.value.trim() == "") {
    alert("Inserisci tutti i dati per inviare la richiesta");
    return;
  }
  if (!validateEmail(ticketemail.value.trim())) {
    alert("Inserisci un'email valida");
    return;
  }
  document.querySelector("[data-sendticket]").disabled = true;
  let msg = "";
  var idOrder;
  var NomeProdotto;
  var SO;
    
  if(info['logged'] === true){
    idOrder =  ordiniselect.options[ordiniselect.selectedIndex].dataset.idordine.trim();
    NomeProdotto = ordiniselect.options[ordiniselect.selectedIndex].dataset.nome.trim();
    SO = ordiniselect.options[ordiniselect.selectedIndex].value.trim() ?? "";
  }
  
  let toSend = "";
  if (!isassistenza.checked || SO == "" || SO == "null") {
    msg = `Richiesta Cliente`;
    toSend = messaggioticket.value.trim();
  } else {
    msg = `(-${SO}-) ${nome} ${cognome} `;
    toSend = `Email Cliente: ${ticketemail.value.trim()}\nId Ordine: ${idOrder}\nNome Prodotto: ${NomeProdotto}\nRichiesta: ${messaggioticket.value.trim()}`;
  }
  fetchDatas("https://kloe.zenchat.it/ajax/creaTicketDaSofia.php", "POST", {
    oggetto: msg,
    messaggio: toSend,
    CC: ticketemail.value.trim(),
  }).then(async (res) => {
    const data = await res.json();
    if (data.ok) {
      AggiungiMessaggio(
        "Ticket inviato correttamente, riceverai risposta da uno dei nostri operatori via mail",
        false
      );
      CloseSendTicket();
    } else {
      alert("Non funziona");
    }
  });
}

const validateEmail = (email) => {
  return String(email)
    .toLowerCase()
    .match(
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
};

function IncrementaContatoreNonRisposte() {
  let alreadyThere = localStorage.getItem("sofia__nr") ?? 0;
  alreadyThere = parseInt(alreadyThere);
  localStorage.setItem("sofia__nr", alreadyThere + 1);
  if (alreadyThere + 1 >= 1) {
    AggiungiMessaggio(
      "Puoi inviare un ticket per ricevere risposta da un operatore",
      false
    );
    localStorage.setItem("sofia__nr", 0);
    setTimeout(() => {
      OpenSendTicket();
    }, 2500);
  }
}

function CheckExecutable(text) {
  const arrExec = text.match(/<<execute: .+?>>/gi) ?? [];
  if (arrExec.length == 0) return text;
  let toExec = [];
  [...arrExec].forEach((exec) => {
    exec = exec.replace("<<execute:", "").replace(">>", "").trim();
    toExec = [...toExec, exec];
  });

  toExec.forEach((e) => {
    setTimeout(() => {
      switch (e.toLowerCase()) {
        case "opensendticket":
          OpenSendTicket();
          break;
        case "connetticonoperatore":
          ConnettiConOperatore();
          break;
        case "opentracking":
          OpenTracking();
          break;
        default:
          break;
      }
    }, 1500);
  });

  return text.replace(/<<execute: .+?>>/gi, "");
}

function InOrarioDiLavoro() {
  let d = new Date();
  let hours = d.getHours();
  let day = d.getDay();

  let ore = (hours >= 9 && hours <= 12) || (hours >= 14 && hours <= 17);
  let giorni = day >= 1 && day <= 5;
  return ore && giorni;
}

async function freeOperators() {
  const res = await fetch("https://kloe.zenchat.it/ajax/getFreeOperators.php");
  const text = await res.text();
  return text == "true";
}

function ControllaSessione() {
  let sessione = localStorage.getItem("chat__session") ?? "";
  let lastSessione = localStorage.getItem("chat__last_session") ?? null;

  if (localStorage.getItem("chat__msgOperator") == null)
    localStorage.setItem("chat__msgOperator", JSON.stringify("[]"));

  if (sessione == "" || OneHourPassed(lastSessione)) {
    let d = new Date();
    localStorage.setItem(
      "chat__session",
      d.getMilliseconds() + d.getTime() + uuidv4()
    );
    localStorage.setItem("chat__last_session", d.toISOString());
    localStorage.setItem("chat__msgOperator", JSON.stringify("[]"));
  }
}


async function ConnettiConOperatore() {
  const freeOp = await freeOperators();

  if (!InOrarioDiLavoro() || !freeOp) {
    OpenSendTicket();
    AggiungiMessaggio(
      "Al momento nessun operatore è disponibile, puoi inviare un ticket dalla schermata di assistenza",
      false
    );
    return;
  }
  AggiungiMessaggio(
    "Manda un messaggio, un operatore ti risponderà al più presto quì in chat, ricorda che puoi sempre scrivere /stop per smettere di parlare con l'operatore",
    false
  );

  if (await OttieniChat()) localStorage.removeItem("chat__session");
  ControllaSessione();
  InviaMessaggio = InviaMessaggioOperatore;
}

function CreaChatRecord(messaggio) {
  fetchDatas("https://kloe.zenchat.it/ajax/creaChatRecord.php", "POST", {
    session: localStorage.getItem("chat__session"),
    messaggio,
  }).then(async (res) => {
    stopChat = true;
    AggiungiMessaggio("In attesa di un operatore...", false);
    SalvaMessaggio("In attesa di un operatore...", false);
    StartUpdatingMessages();
  });
}

async function OttieniChat() {
  const formData = new FormData();
  formData.append("session", localStorage.getItem("chat__session"));
  const res = await fetch("https://kloe.zenchat.it/ajax/ottieniChat.php", {
    method: "POST",
    body: formData,
  });
  const text = await res.text();
  return text != "needToCreate";
}

const InviaMessaggioOperatore = async () => {
  const messaggio = chat__textarea.value.trim();
  if (stopChat && !messaggio.includes("/stop")) {
    alert("Aspetta che un operatore ti risponda o chiudi la chat con /stop");
    return;
  }

  AggiungiMessaggio(messaggio, true);
  SalvaMessaggio(messaggio, true);
  chat__textarea.value = "";

  if (messaggio.includes("/stop")) {
    ChiudiChat(localStorage.getItem("chat__session"));
    FermaConnessioneAllaChat("La chat con l'operatore è stata chiusa");
    return;
  }

  const created = await OttieniChat();
  if (!created) {
    CreaChatRecord(messaggio);
    return;
  }

  fetchDatas("https://kloe.zenchat.it/ajax/sendMessageToChat.php", "POST", {
    session: localStorage.getItem("chat__session"),
    messaggio,
    pageUser: window.location.href
  });
};

function uuidv4() {
  return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
    (
      c ^
      (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
    ).toString(16)
  );
}

function StartUpdatingMessages() {
  updateInterval = setInterval(() => {
    fetchDatas("https://kloe.zenchat.it/ajax/getMessages.php", "POST", {
      session: localStorage.getItem("chat__session"),
    }).then(async (res) => {
      const response = await res.text();
      const currentMessages = localStorage.getItem("chat__msgOperator");
      const messages = response.split("<|!sep!|>");
      [...messages].forEach((msg) => {
        if (currentMessages.includes(msg) || msg == "") return;
        else {
          if (
            msg.trim() == "operatore: Questa chat è stata chiusa dall'operatore"
          ) {
            FermaConnessioneAllaChat(msg.replace(/operatore:/gi, "").trim());
            return;
          }
          if (msg.includes("utente: ")) return;
          AggiungiMessaggio(msg.replace(/operatore:/gi, "").trim(), false);
          SalvaMessaggio(msg.replace(/operatore:/gi, "").trim(), false);
          stopChat = false;
        }
      });
      localStorage.setItem("chat__msgOperator", JSON.stringify(messages));
    });
  }, 1000);
}

function stopUpdatingMessages() {

  clearInterval(updateInterval);
}

function ChiudiChat(session) {
  fetchDatas("https://kloe.zenchat.it/ajax/chiudiChat.php", "POST", {
    session,
    user: 1,
  });
}

function FermaConnessioneAllaChat(msg) {
  stopUpdatingMessages();
  InviaMessaggio = sendToSofia;
  localStorage.removeItem("chat__session");
  AggiungiMessaggio(msg, false);
  SalvaMessaggio(msg, false);
  AggiungiMessaggio(
    "Ciao sono Sofia, l'assistente virtuale di Onlinestore!",
    false
  );
  SalvaMessaggio(
    "Ciao sono Sofia, l'assistente virtuale di Onlinestore!",
    false
  );
}

async function getTrackingOrdini() {
  const req = await fetch("ajax/functions.php");
  const json = await req.json();
  return json;
}

async function OpenTracking() {
  CloseAll();
  if (sofiaChat.classList.contains("closed"))
    sofiaChat.classList.remove("closed");
  if (!chat__sidebar.classList.contains("open")) chat__sideopen.click();
  const swiperTracking = document.querySelector(".swiper-wrapper.tracking");
  const info = await getInfoCliente();
  const { logged, messaggio, email, nome, cognome, ordini } = info;
  if (!logged) {
    alert("Per vedere i tuoi ordini devi aver effettuato l'accesso");
    CloseAll();
    return;
  }
  swiperTracking.innerHTML = "";
  const trackingOrdini = await getTrackingOrdini();
  if ([...trackingOrdini.arr].length == 0) {
    alert("Non hai ancora effettuato ordini");
    return;
  }
  InserisciProdottiTracking(trackingOrdini);

  chat__sidebar.setAttribute("data-showtracking", true);
}

function InserisciProdottiTracking(trackingOrdini) {
  const swiperTracking = document.querySelector(".swiper-wrapper.tracking");

  [...trackingOrdini.arr].forEach((prodotto) => {
    const slide = document.createElement("div");
    slide.className = "swiper-slide";
    let url = prodotto.url;
    if (url.includes("@")) url = url.replace("@", prodotto.tracking);
    else url += prodotto.tracking;

    slide.innerHTML += `
    <div data-prodotto-tracking>
      <img src="${prodotto.img}">  
      <div>
      <p data-titolo>${prodotto.nome.substr(0, 30).trim()}...</p>

      <p data-stato><b>Stato:</b> ${
        prodotto.tracking.trim() == ""
          ? "Il tuo pacco è stato preso in carico dal corriere locale, il tracking sarà disponibile a breve"
          : prodotto.stato
      }</p>

      <p data-tracking style="${
        prodotto.tracking.trim() == "" ? "display:none;" : ""
      }"><b>Tracking:</b> ${prodotto.tracking}</p>

      <p data-corriere style="${
        prodotto.tracking.trim() == "" ? "display:none;" : ""
      }"><b>Spedito con: </b>${prodotto.nomeCorriere}</p>

      <button data-url style="${
        url.trim() == "" ? "display:none;" : ""
      }" onclick="OpenUrl('${url.trim()}', '${prodotto.tracking.trim()}')">${
      url.trim() != prodotto.tracking.trim() ? "Altre info" : "Copia tracking"
    }</button>

      </div>
    </div>
    `;

    swiperTracking.append(slide);
  });

  var sliderSwiper = new Swiper(".slideTrackingSofia", {
    loop: true,
    pagination: {
      el: ".pag-tracking",
    },
  });
}

function CLoseTracking() {
  chat__sidebar.removeAttribute("data-showtracking");
}

function OpenUrl(url, tracking) {
  if (url != tracking) {
    const a = document.createElement("a");
    a.href = url;
    a.target = "_blank";
    a.click();
  } else {
    CopiaTracking(tracking);
  }
}

function CloseAll() {
  CloseSendTicket();
  CLoseTracking();
}

async function CopiaTracking(tracking) {
  await navigator.clipboard.writeText(tracking);
  alert("Numero di tracking del prodotto copiato");
}

async function fetchDatas(url, method = "GET", objBody = {}) {
  const formData = new FormData();
  Object.keys(objBody).forEach((key) => {
    formData.append(key, objBody[key]);
  });
  let res;
  if (method == "POST") {
    res = await fetch(url, {
      method: method,
      body: formData,
    });
  } else {
    res = await fetch(url);
  }
  return res;
}
