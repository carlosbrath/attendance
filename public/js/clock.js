const hr = document.getElementById("hr");
const min = document.getElementById("min");
const sec = document.getElementById("sec");

function rotation(target, value) {
  target.style.transform = `rotate(${value}deg)`;
}

function clock() {
  const date = new Date();
  let hh = (date.getHours() % 12) + date.getMinutes() / 60;
  let mm = date.getMinutes();
  let ss = date.getSeconds();

  hh *= 30;
  mm *= 6;
  ss *= 6;

  rotation(hr, hh);
  rotation(min, mm);
  rotation(sec, ss);
}

setInterval(clock, 500);
