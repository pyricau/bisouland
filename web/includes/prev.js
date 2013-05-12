var timer=0;
var ptag=String.fromCharCode(5,6,7);
function  previsualisation() {
t=document.formulaire.textarea.value 
t=code_to_html(t)
if (document.getElementById) document.getElementById("prev").innerHTML=t
if (document.formulaire.auto.checked) timer=setTimeout(previsualisation,1)
<!-- le "1" est le temps que met le texte à s'afficher, "1" : le texte s'affichera en même temps que l'on écrit (en s) -->
}
function automatique() {
if (document.formulaire.auto.checked) previsualisation() <!-- si on a cocher la case d'aperçu automatique -->
}
function code_to_html(t) {
t=nl2khol(t)
// balise Gras <!-- on lui dis que telles balises correspondent à tels codes en HTML -->
t=deblaie(/(\[\/b\])/g,t)
t=remplace_tag(/\[b\](.+)\[\/b\]/g,'<span style="font-weight: bold;">$1</span>',t) 
t=remblaie(t)

// balise Italic
t=deblaie(/(\[\/i\])/g,t)
t=remplace_tag(/\[i\](.+)\[\/i\]/g,'<span style="font-style: italic;">$1</span>',t) 
t=remblaie(t)


// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/o_O/g,'<img src="/smileys/blink.gif" alt="" />',t) 
t=remblaie(t)

// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/;\)/g,'<img src="/smileys/clin.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:D/g,'<img src="/smileys/heureux.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/\^\^/g,'<img src="/smileys/hihi.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:o/g,'<img src="/smileys/huh.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:p/g,'<img src="/smileys/langue.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:colere:/g,'<img src="/smileys/mechant.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:noel:/g,'<img src="/smileys/noel.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:lol:/g,'<img src="/smileys/rire.gif" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:-°/g,'<img src="/smileys/siffle.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:\)/g,'<img src="/smileys/smile.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:\(/g,'<img src="/smileys/triste.png" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:euh:/g,'<img src="/smileys/unsure.gif" alt="" />',t) 
t=remblaie(t)
// smilies Smile <!-- on oublie pas les smilies -->
t=remplace_tag(/:coeur:/g,'<img src="/images/puce.png" alt="" />',t) 
t=remblaie(t)

t=unkhol(t)
t=nl2br(t)
return t
}
<!-- tout le code qui suit c'est pour transformer toutes les balises, comme les preg_replace en PHP -->
function deblaie(reg,t) {
textarea=new String(t);
return textarea.replace(reg,'$1\n');
}
function remblaie(t) {
textarea=new String(t);
return textarea.replace(/\n/g,'');
}
function remplace_tag(reg,rep,t) {
textarea=new String(t);
return textarea.replace(reg,rep);
}
function nl2br(t) {
textarea=new String(t);
return textarea.replace(/\n/g,'<br/>');
}
function nl2khol(t) {
textarea=new String(t);
return textarea.replace(/\n/g,ptag);
}
function unkhol(t) {
textarea=new String(t);
return textarea.replace(new RegExp(ptag,'g'),'\n');
}   


function bbcode(bbdebut, bbfin)
{
var input = window.document.formulaire.textarea;
input.focus();
/* pour IE (toujous un cas appar lui ;) )*/
if(typeof document.selection != 'undefined')
{
var range = document.selection.createRange();
var insText = range.text;
range.text = bbdebut + insText + bbfin;
range = document.selection.createRange();
if (insText.length == 0)
{
range.move('character', -bbfin.length);
}
else
{
range.moveStart('character', bbdebut.length + insText.length + bbfin.length);
}
range.select();
}
/* pour les navigateurs plus récents que IE comme Firefox... */
else if(typeof input.selectionStart != 'undefined')
{
var start = input.selectionStart;
var end = input.selectionEnd;
var insText = input.value.substring(start, end);
input.value = input.value.substr(0, start) + bbdebut + insText + bbfin + input.value.substr(end);
var pos;
if (insText.length == 0)
{
pos = start + bbdebut.length;
}
else
{
pos = start + bbdebut.length + insText.length + bbfin.length;
}
input.selectionStart = pos;
input.selectionEnd = pos;
}
/* pour les autres navigateurs comme Netscape... */
else
{
var pos;
var re = new RegExp('^[0-9]{0,3}$');
while(!re.test(pos))
{
pos = prompt("insertion (0.." + input.value.length + "):", "0");
}
if(pos > input.value.length)
{
pos = input.value.length;
}
var insText = prompt("Veuillez taper le texte");
input.value = input.value.substr(0, pos) + bbdebut + insText + bbfin + input.value.substr(pos);
}
}
function smilies(img)
{
window.document.formulaire.textarea.value += '' + img + '';
}