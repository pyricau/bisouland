  function t(){
                var compteur=document.getElementById('compteur');
                s=duree;
                m=0;h=0;
                if(s<0){
					compteur.innerHTML=fin+"<br />"+"<a href="+adresseFin+">"+next+"</a>"
					//compteur.innerHTML="<a href="+adresseFin+">"+next+"</a>"
                }else{
                  if(s>59){
                    m=Math.floor(s/60);
                    s=s-m*60
                  }
                  if(m>59){
                    h=Math.floor(m/60);
                    m=m-h*60
                  }
                  if(s<10){
                    s="0"+s
                  }
                  if(m<10){
                    m="0"+m
                  }
                  compteur.innerHTML=h+":"+m+":"+s+"<br /><a href="+adresseStop+">"+stop+"</a>"
				  //compteur.innerHTML=h+":"+m+":"+s
                }
                duree=duree-1;
				
				window.setTimeout("t();",999);
				
				if(nbCompteur>1)
				{
					t2();
				}
              }
function t2(){
                var compteur2=document.getElementById('compteur2');
                s2=duree2;
                m2=0;h2=0;
                if(s2<0){
                  compteur2.innerHTML=fin2+"<br />"+"<a href="+adresseFin2+">"+next2+"</a>"
				  //compteur2.innerHTML="<a href="+adresseFin2+">"+next2+"</a>"
                }else{
                  if(s2>59){
                    m2=Math.floor(s2/60);
                    s2=s2-m2*60
                  }
                  if(m2>59){
                    h2=Math.floor(m2/60);
                    m2=m2-h2*60
                  }
                  if(s2<10){
                    s2="0"+s2
                  }
                  if(m2<10){
                    m2="0"+m2
                  }
                  compteur2.innerHTML=h2+":"+m2+":"+s2+"<br /><a href="+adresseStop2+">"+stop2+"</a>"
				  //compteur2.innerHTML=h2+":"+m2+":"+s2
                }
                duree2=duree2-1;
				
				if(nbCompteur>2)
				{
					t3();
				}
				
              }
			  
function t3(){
                var compteur3=document.getElementById('compteur3');
                s3=duree3;
                m3=0;h3=0;
                if(s3<0){
                  compteur3.innerHTML=fin3+"<br />"+"<a href="+adresseFin3+">"+next3+"</a>"
				  //compteur3.innerHTML="<a href="+adresseFin3+">"+next3+"</a>"
                }else{
                  if(s3>59){
                    m3=Math.floor(s3/60);
                    s3=s3-m3*60
                  }
                  if(m3>59){
                    h3=Math.floor(m3/60);
                    m3=m3-h3*60
                  }
                  if(s3<10){
                    s3="0"+s3
                  }
                  if(m3<10){
                    m3="0"+m3
                  }
				  compteur3.innerHTML=h3+":"+m3+":"+s3+"<br /><a href="+adresseStop3+">"+stop3+"</a>"
                  //compteur3.innerHTML=h3+":"+m3+":"+s3
                }
                duree3=duree3-1;

              }