


/*****************************Formulaires*********************************/
$('.selectChoice').change(function (){
  this.form.submit();
})
$('.offset-limit').change(function (){
  this.form.submit();
})
/*************************Sidebar*************************************/

let sidebar = document.querySelector(".sidebar");
let sub_menu= document.querySelectorAll('.sub-menu');
let content= document.querySelector('.home_content');

$('#btn').on("click",function(){
  sidebar.classList.toggle("active");
  content.classList.toggle('active');
  sub_menu.forEach(item =>{
    item.classList.remove('active');
  })
});

document.querySelectorAll('.menu').forEach(item => {
  item.addEventListener('click', event => {
    sub_menu.forEach(item =>{
      item.classList.remove('active');
    });
    item.parentElement.querySelectorAll('.sub-menu').forEach(item =>{
        
      item.classList.toggle('active');
      sidebar.classList.add("active");
      content.classList.add('active');
    });
      
  });
});


/**************************tri tableau*********************************/
const compare = (ids, asc) => (row1, row2) => {
    const tdValue = (row, ids) => row.children[ids].textContent;
    const tri = (v1, v2) => v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2);
    return tri(tdValue(asc ? row1 : row2, ids), tdValue(asc ? row2 : row1, ids));
  };
  
  const tbody = document.querySelector('.table_body');
  const thx = document.querySelectorAll('th');
  const trxb = tbody.querySelectorAll('tr');
  thx.forEach(th => th.addEventListener('click', () => {
    let classe = Array.from(trxb).sort(compare(Array.from(thx).indexOf(th), this.asc = !this.asc));
    classe.forEach(tr => tbody.appendChild(tr));
  }));

/*************************MODAL NOTIFICATION************************************* */
let y=50;
let x=0;

document.querySelectorAll('.modal_notification').forEach(item =>{
 
  (item.querySelector('.modal_text').innerText.length >0)?item.classList.add('active'):'';
  
  item.style.transform = 'translate('+x+'%,'+y+'%)';
  y=y+20;
  x=x-7;
  console.log(item.style.transform);
  item.querySelector('i').addEventListener('click',function(){
    item.classList.remove('active');
    deleteCookie('message','/',location.hostname);
  })

})
/************gestion des cookies */
function deleteCookie( name, path, domain ) {

  if( get_cookie( name )) {
    // $('.modal_text'))innerText= ;
    document.cookie = name + "=" +
      ((path) ? ";path="+path:"")+
      ((domain)?";domain="+domain:"") +
      ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
  }
}

function get_cookie(name){
    return document.cookie.split(';').some(c => {// on teste si le string commence bien par le nom 
        return c.trim().startsWith(name + '=');
    });
}

/*********************charts************************** */

let sorties =new Array();
document.querySelectorAll('.Sorties').forEach(item=>{
  sorties.push(item.innerText);
});
let securite =new Array();
document.querySelectorAll('.stock_de_securite').forEach(item=>{
  securite.push(item.innerText);
});
let marge =new Array();
document.querySelectorAll('.Marge_beneficiaire').forEach(item=>{
  marge.push(item.innerText);
});
let entree =new Array();
document.querySelectorAll('.EntréesDivers').forEach(item=>{
  entree.push(item.innerText);
});
let labels =new Array();
document.querySelectorAll('.reference_article').forEach(item=>{
  labels.push(item.innerText);
});

let livraisons =new Array();
document.querySelectorAll('.Livraison').forEach(item=>{
  livraisons.push(item.innerText);
});

const ctx = document.getElementById('myChart');
const myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Sorties',
      data: sorties,
      backgroundColor: [
        'marroon'
      ],
      borderColor: [
        'marroon',
      ],
      borderWidth: 1
    },
    {
      label: 'Stock disponible',
      data: marge,
      backgroundColor: [
        'green'
      ],
      borderColor: [
        'green',
      ],
      borderWidth: 1
    }]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Répartition du volume de marchandises'
      },
    },
    responsive: true,
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true
      }
    }
  }
});



const ctxj = document.getElementById('myChartj');
const myChartj = new Chart(ctxj, {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      label: 'Livraisons',
      data: livraisons,
      backgroundColor: [
          'yellow','red','blue','green','pink','grey','white','black','orange','purple'
      ],
      borderColor: [
          '',
      ],
      borderWidth: 0
    },]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Répartition des livraisons'
      },
    },
    responsive: true,
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true
      }
    }
  }
});

const ctxj2 = document.getElementById('myChartj2');
const myChartj2 = new Chart(ctxj2, {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      label: 'Entrées',
      data: sorties,
      backgroundColor: [
        'green','pink','grey','white','black','orange','purple','yellow','red','blue'
      ],
      borderColor: [
        ''
      ],
      borderWidth: 0
    },]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Répartition des sorties'
      },
    },
    responsive: true,
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true
      }
    }
  }
});
const ctxj3 = document.getElementById('myChartj3');
const myChartj3 = new Chart(ctxj3, {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label:'Securite',
      data: securite,
      backgroundColor: [
        'green','green','purple','yellow','red','blue','green','pink','grey','white','black'
      ],
      borderColor: [
        'green','green','purple','yellow','red','blue','green','pink','grey','white','black'
      ],
      borderWidth: 1
    },
    {
      label: 'Disponible',
      data: marge,
      backgroundColor: [
        'red','red','purple','yellow','red','blue','green','pink','grey','white','black'
      ],
      borderColor: [
        'red','red','purple','yellow','red','blue','green','pink','grey','white','black'
      ],
      borderWidth: 1
    },]
  },
  options: {
    plugins: {
      title: {
        display: true,
        text: 'Ecart-type des besoins par article'
      },
    },
    responsive: true,
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true
      }
    }
  }
});

/*
function recupererDonnee(url,methodes,donneesDesMethodes){//recupère les données au format JSON à partir de l'url et passe son resultat à des méthodes qui recevoint d'autres donneesDesMethodes
    //"https://polenumerique.re/dl/dwwm2021/ws/m1s3/"; //"https://polenumerique.re/dl/dwwm2021/ws/m1s4/?q=pc";
    var xhr= new XMLHttpRequest();
    
    xhr.onreadystatechange= function (){
        if(this.readyState!=4 || this.status!=200){//etat pour en cours de chargement qui se charge de l'afficher sur la page avec des spinners
            $("#demo").html('<td class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td><td class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td><td class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td>');  
        }
        setTimeout(() => {
            if(this.readyState!=4 || this.status!=200){//etat pour echec de chargement avec message d'erreur sur la page  
                $("#demo").html("<p style='color:orangered'>Le temps de chargement a été trop long. Veuillez recharger la page</p>");
            }   
        }, 10000);
        
        if(this.readyState==4 && this.status==200){//surveille les changements d'états et attends un succès
            elementJson=xhr.response;//transmet la valeur recu en cas de succès
            for(let i=0;i<methodes.length;i++){ //passe aux méthodes leurs donneesDesMethodes respectif qui a été fournie sous forme de tableau
                methodes[i](elementJson,donneesDesMethodes[i]); 
            }   
        }
    }
    xhr.open("GET",url,true);
    xhr.responseType="json";
    xhr.send();
   
};*/

