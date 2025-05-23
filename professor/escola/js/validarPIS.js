// VERIFICAR O PIS

var ftap="3298765432";
var total=0;
var i;
var resto=0;
var numPIS=0;
var strResto="";

function ChecaPIS(pis)
{

total=0;
resto=0;
numPIS=0;
strResto="";

numPIS=pis;
pis = remove(pis, ".");

pis = remove(pis, "-");

if (numPIS=="" || numPIS==null)
{
return false;
}

for(i=0;i<=9;i++)
{
resultado = (numPIS.slice(i,i+1))*(ftap.slice(i,i+1));
total=total+resultado;
}

resto = (total % 11)

if (resto != 0)
{
resto=11-resto;
}

if (resto==10 || resto==11)
{
strResto=resto+"";
resto = strResto.slice(1,2);
}

if (resto!=(numPIS.slice(10,11)))
{
return false;
}

return true;
}

// VALIDAR O PIS


function ValidaPis()
{
	var pis = document.cadastro.pis.value;

	if (!ChecaPIS(pis))
	{
		alert("PIS INVALIDO");
		return false;
	} else {
		alert("PIS VALIDO");
		return false;
	}
}

