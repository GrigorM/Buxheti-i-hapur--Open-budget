<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="processing.js"></script>
<script type="text/processing" data-processing-target="canvas1">
PFont label;
ArrayList<Budget> budgets;
Integrator[] interpolators;
int currentBudget, pastBudget;
String[] names, names2;
float[] tabLeft, tabRight;
int[] customValues;
boolean activate, showChart;
boolean col1, col2;
//variabla boolean per hyrjen
boolean enter;
boolean info;
Integrator[] colint;
//votat e nej perdoruesi
int[] votes;
//variabla te perdoruesit, id (emri) dhe counti (numri i hereve qe ka votuar)
String fbId;
int count;
//interpolatora per animim;
Integrator[] animinter;
//booleans for animation;
boolean b1, b2, b3;
//vektore qe mbajne te dhena per 1 ze nder vite
int[][] cathegorised;
int index;
Integrator[] detailed;
boolean graph;
//animimi i votimit
Integrator[] upordown;
int pos;
Integrator message;

void setup() {
  size(3*screen.width/5, screen.height-110);
  //emrat e zerave
  names = getNames();
  names2 = getNames2();
  //arraylista me buxhetet
  budgets = new ArrayList<Budget>();
  //ngarko te dhenat te buxhetet
  getData();
  //vlerat e modifikuara te perdoruesve
  customValues = new int[11];
  getCustomData();
  checkSum();
  //index i buxhetit aktual
  currentBudget=budgets.size()-1;
  pastBudget=currentBudget;
  //interpolatoret per animim
  interpolators = new Integrator[15];
  for (int i=0; i<11; i++) {
    float initialValue = budgets.get(currentBudget).values[i][9];
    interpolators[i] = new Integrator(initialValue);
    interpolators[i].attraction=0.1;
  }
  interpolators[11] = new Integrator(budgets.get(currentBudget).deficit);
  interpolators[11].attraction=0.1;
  interpolators[12] = new Integrator(budgets.get(currentBudget).total);
  interpolators[12].attraction=0.1;
  interpolators[13] = new Integrator(0);
  interpolators[13].attraction=0.1;
  interpolators[14] = new Integrator(0);
  interpolators[14].attraction=0.1;
  label = createFont("SegoeUI", 16);
  textFont(label);
  rectMode(CORNERS);
  col1=false;
  col2=false;
  enter=true;
  info=false;
  colint = new Integrator[4];
  colint[0]=new Integrator(255);
  colint[1]=new Integrator(0);
  colint[2]=new Integrator(0);
  colint[3]=new Integrator(0);
  votes = new int[11];
  for(int i=0; i<11; i++){
  	votes[i]=0;
  }
  //interpolatoret per animin;
  animinter = new Integrator[3];
  animinter[0] = new Integrator(110);
  animinter[1] = new Integrator(90);
  animinter[2] = new Integrator(110);
  b1=false;
  b2=false;
  b3=false;
  showChart=false;
  //inicializeo strukturen qe mban te dhena per ndryshimin e nje zeri nder vite
  cathegorised = new int[11][budgets.size()];
  populateCathegories();
  int index=0;
  detailed = new Integrator[budgets.size()];
  for(int i=0; i<budgets.size(); i++){
    detailed[i] = new Integrator(0, 0.5, 0.1);
  }
  graph=true;
  //animimi i votimit
  upordown = new Integrator[2];
  upordown[0]= new Integrator(0, 0.5, 0.1);
  upordown[1] = new Integrator(0, 0.5, 0.1);
  pos=0;
  message = new Integrator(0, 0.5, 0.02);
}
void draw() {
  background(168, 213, 128);
  intro();
  information();
  if (enter==false) {
    drawDataBars(budgets.get(currentBudget));
    drawTitleTabs();
    drawBudgetTab();
    customTab();
	showCharts();
    if (interpolators[14].value > 5) {
      drawCustomBudget();
    }
    if (interpolators[13].value > 5) {
      drawTotalBudget();
    }
  }
  updateInterpolators();
  displayMessage();
}
void updateInterpolators() {
  for (int i=0; i<11; i++) {
    if (enter==true){
      interpolators[i].update(0);}
    else{
      interpolators[i].update(budgets.get(currentBudget).values[i][9]);}
  }
  interpolators[11].update(budgets.get(currentBudget).deficit);
  interpolators[12].update(budgets.get(currentBudget).total);
  if (col1==true){
    interpolators[13].update(200);
  }
  else {interpolators[13].update(0);}
  if (col2==true){
    interpolators[14].update(200);}
  else {interpolators[14].update(0);}
  if ((enter) && (!info)) {
    colint[0].update(255);
    colint[1].update(0);
    colint[2].update(0);
    if(b1) {animinter[0].update(130); }
    else {animinter[0].update(110);}
    if(b2) {animinter[1].update(110);}
    else {animinter[1].update(90);}
  }
  else if ((enter) && (info)) {
    colint[0].update(0);
    colint[1].update(255);
    colint[2].update(0);
    if(b3) {animinter[2].update(130);}
    else {animinter[2].update(110);} 
  }
  else if (!enter) {
    colint[2].update(255);
    colint[0].update(0);
    colint[1].update(0);
  }
  if(showChart){
    colint[3].update(255);
  }
  else colint[3].update(0);
  upordown[0].update(0);
  upordown[1].update(0);
  message.update(0);
}
void getData() {
  String[] lines = loadStrings("http://tablo-al.com/dataLoader2.php");
  int viti=2011;
  int c=0;
  while (c<lines.length) {
    int[][] vals = new int[11][10];
    for(int i=0; i<10; i++){
      for(int j=0; j<10; j++){
        vals[i][j] = int(lines[10*i+j+c]);
      }
    }
    for(int i=0; i<9; i++){
      vals[10][i]=0;
    }
    vals[10][9] = int(lines[c+100]);
    int def = int(lines[c+101]);
	int tot=0;
	for(int i=0; i<=10; i++){
      tot += vals[i][9];
	}
    Budget b = new Budget(str(viti), vals, tot, def);
	budgets.add(b);
    viti++;
    c+=102;
  }
}
void populateCathegories(){
  for(int j=0, n=budgets.size(); j<n; j++){
    for(int i=0; i<11; i++){
      cathegorised[i][j] = budgets.get(j).values[i][9];
    }
  }
}
String[] getNames() {
  String[] returnArray = new String[11];
  returnArray[0] = "Shërbimet e\nPërgjithshme\nPublike"; 
  returnArray[1] = "Mbrojtja"; 
  returnArray[2] = "Rendi dhe\nSiguria\nPublike"; 
  returnArray[3] = "Çështjet\nEkonomike"; 
  returnArray[4] = "Mbrojtja e\nMjedisit"; 
  returnArray[5] = "Strehimi dhe\nKomoditetet\ne Komunitetit"; 
  returnArray[6] = "Shëndetësia"; 
  returnArray[7] = "Argëtimi,\nKultura dhe\nÇështjet Fetare"; 
  returnArray[8] = "Arsimi"; 
  returnArray[9] = "Mbrojtja\nSociale"; 
  returnArray[10] = "Shpenzime të\ntjera të\npaklasifikuara";
  return returnArray;
}
String[] getNames2() {
  String[] returnArray = new String[11];
  returnArray[0] = "Shërbimet e përgjithshme publike"; 
  returnArray[1] = "Mbrojtja"; 
  returnArray[2] = "Rendi dhe siguria publike"; 
  returnArray[3] = "Çështjet ekonomike"; 
  returnArray[4] = "Mbrojtja e mjedisit"; 
  returnArray[5] = "Strehimi dhe komoditetet e komunitetit"; 
  returnArray[6] = "Shëndetësia"; 
  returnArray[7] = "Argëtimi, kultura dhe çështjet fetare"; 
  returnArray[8] = "Arsimi"; 
  returnArray[9] = "Mbrojtja sociale"; 
  returnArray[10] = "Shpenzime të tjera të paklasifikuara";
  return returnArray;
}
void checkSum() {
  Budget b = budgets.get(budgets.size()-1);
  int sum=0;
  int sum2=0;
  for (int i=0; i<11; i++) {
    sum+=b.values[i][9];
    sum2+=customValues[i];
  }
  if (sum2<sum) {
    int diff = sum-sum2;
    for (int i=0; i<11; i++) {
      customValues[i]+=diff/11;
    }
    String s1="v0="+customValues[0]+"&v1="+customValues[1]+"&v2="+customValues[2]+"&v3="+customValues[3]+"&v4="+customValues[4]+"&v5="+customValues[5]+"&v6="+customValues[6];
    String s2="&v7="+customValues[7]+"&v8="+customValues[8]+"&v9="+customValues[9]+"&v10="+customValues[10]+"&v11="+customValues[11];
    loadStrings("http://tablo-al.com/newyear.php?"+s1+s2);
  }
}

void drawDataBars(Budget b) {
  fill(173, 32, 60, colint[2].value);
  //fill(35, 155, 215, colorinterpolator[2].value);
  //fill(0);
  noStroke();
  for (int i=0; i<11; i++) {
    float value = interpolators[i].value;
    float x = map(i, 0, 10, 10, width-60);
    float y = map(value, 100000, 180000000, height-100, 20);
    pushStyle();
    if((mouseX>x) && (mouseX<(x+50)) && (mouseY>y) && (mouseY<(height-50))){
    fill(255, 50, 80, colint[2].value);
	}
    rect(x, y, x+50, height-50);
    fill(100, colint[2].value);
    textAlign(CENTER, TOP);
	textSize(10);
    text(budgets.get(currentBudget).values[i][9], x+25, y-15);
    text(names[i], x+25, height-45);
	if((mouseX>x) && (mouseX<(x+50)) && (mouseY>y) && (mouseY<(height-50))){
    fill(255, colint[2].value);
	textSize(16);
	text(nf(100*interpolators[i].value/interpolators[12].value, 1, 1)+"%", x+25, height-80);
	}
    popStyle();
  }
}

void drawTitleTabs() {
//modifikuar
  float tabPad=width/56;
  pushStyle();
  noStroke();
  textSize(16);
  textAlign(LEFT);
  fill(0);
  text("Viti ", width/(42.5), 30);
  if (tabLeft==null) {
    tabLeft=new float[budgets.size()];
    tabRight=new float[budgets.size()];
  }
  float runningX=width/10.625;
  float tabTop=50-textAscent()-15;
  float tabBottom=50;
  for (int i=0; i<budgets.size(); i++) {
    Budget b=budgets.get(i);
    String title=b.year;
    tabLeft[i]=runningX;
    float titleWidth=textWidth(title);
    tabRight[i]=tabLeft[i]+tabPad+titleWidth+tabPad;
    fill(i==currentBudget? 0:160);
    text(title, runningX+tabPad, 30);
    runningX=tabRight[i];
  }
  popStyle();
}

void drawBudgetTab() {
  pushStyle();
  if(mouseX>15 && mouseX<200 && mouseY>60 && mouseY<110)
  fill(150, 210, 110);
  else
  fill(168, 213, 128);
  stroke(150, 200, 110);
  rectMode(CORNER);
  rect(15, 60, 185, 50);
  textSize(16);
  textAlign(LEFT);
  fill(0);
  text("Buxheti total: " + budgets.get(currentBudget).total, 20, 80);
  textSize(12);
  text("(të dhënat janë në mijë lekë)", 20, 100);
  popStyle();
}
void mousePressed() {
  if (info==true) {
    if (dist(mouseX, mouseY, width/2, height/2+140)<55) {
      enter=false;
      info=false;
    }
  }
  if (enter==true) {
    if (dist(mouseX, mouseY, width/2, height/2-40)<55) {
      enter=false;
    }
    else if (dist(mouseX, mouseY, width/2, height/2+120)<45) {
      info=true;
    }
  }
  if (mouseY>10 && mouseY<45) {
    for (int i=0; i<budgets.size(); i++) {
      if (mouseX>tabLeft[i] && mouseX<tabRight[i]){
        setCurrent(i);}
    }
  }
  //shfaq buxhetin total
  else if (mouseY<110 && mouseY>60 && mouseX>15 && mouseX<200) {
      if (col2==false){
        col1=true;}
  }
  else if (details()) {
    if (col2==false) {
      for (int i=0; i<11; i++) {
        float tempY = map(interpolators[i].value, 100000, 180000000, 550, 20);
        if (mouseX>10 + i*(width-70)/10 && mouseX<60 + i*(width-70)/10) {
          if (mouseY>tempY && mouseY<(height-50)) {
           if (!showChart){
              showChart=true;
			}
			index=i;
          }
        }
      }
    }
  }
  else if(mouseX>570 || mouseX<20 || mouseY>340){
  	showChart=false;
  }
  //heq buxhetin total
  else {
    col1=false;
	//showChart=false;
  }
  if(showChart){
  	if(dist(mouseX, mouseY, 530, 160)<15){
		graph=true;
	}
	else if(dist(mouseX, mouseY, 530, 220)<15){
		graph=false;
	}
  }
  //shfaq interfacin e votimit;
  if (col1==false) {
    if (mouseY<135 && mouseY>110) {
      if (mouseX>15 && mouseX<180) {
        if(fbId!=null){
        col2=true;
        activate=true;
        }
        else{
            message.set(255);
        }
      }
    }
    else if (mouseY>(height-70) && mouseY<(height-56)) {
      if (activate && count<10) {
        for (int i=0; i<11; i++) {
          if (mouseX>(16+i*(width-70)/10) && mouseX<(30+i*(width-70)/10)) {
            updateVotes(i, -1);
          }
          else if (mouseX>(40+i*(width-70)/10) && mouseX<(54+i*(width-70)/10)) {
            updateVotes(i, 1);
          }
        }
      }
    }
    else if (dist(mouseX, mouseY, 95, height/2-50)<110) {
        //thirr nje faqe qe ben modifikimin e tabeles me te dhenat e buxhetit, dhe countin e perdoruesit qe sapo votoi.
        //cout-i i ri dhe variablat e votimit do kalohen me query string
        //String[] lines = loadStrings("http://localhost/updateData.php?count="+count+"&")
		//95, height/2-50, 110, 110
		sendFeedback();
    }
    //largo nderfaqen e votimit
    else {
      col2=false;
      activate=false;
    }
  }
}
void displayMessage(){
    pushStyle();
    fill(0, message.value);
    textSize(22);
    textAlign(CENTER, CENTER);
    text("Ju lutemi, logohuni.", width/2, height/2-100);
    popStyle();
}
void setCurrent(int cb) {
  currentBudget=cb;
}

void drawTotalBudget() {
  fill(168, 213, 128, interpolators[13].value);
  rect(0, 50, width, height);
  float h = map(interpolators[12].value, 75000000, 750000000, 0, 200);
  float w1 = interpolators[11].value/interpolators[12].value;
  fill(255, 89, 89, interpolators[13].value);
  rect(0, height/2-h, w1*width, height/2+h);
  fill(64, 148, 178, interpolators[13].value);
  rect(w1*width, height/2-h, width, height/2+h);
  pushStyle();
  textAlign(LEFT);
  fill(0, interpolators[13].value);
  textSize(12);
  text("(të dhënat janë në mijë lekë)", 20, 100);
  textSize(16);
  text("Buxheti total: " + budgets.get(currentBudget).total, 20, 80);
  text("Jep mendimin tend", 20, 130);
  text("Defiçiti: "+budgets.get(currentBudget).deficit, 10, height/2-h-20);
  textAlign(CENTER);
  text("Totali i të ardhurave: "+ budgets.get(currentBudget).incomes, (width*w1+width)/2, height/2-h-20);
  text(nf(w1*100, 2, 2) + "%", w1*width/2, height/2);
  text(nf((1-w1)*100, 2, 2)+"%", (width*w1+width)/2, height/2);
  popStyle();
}

void getCustomData() {
  String[] lines=loadStrings("http://tablo-al.com/customDataLoader.php");
  for (int i=0; i<11; i++) {
    customValues[i] = int(lines[i]);
  }
}
void customTab() {
  pushStyle();
  textSize(16);
  textAlign(LEFT);
  fill(0);
  text("Jep mendimin tënd", 20, 130);
  popStyle();
}
void drawCustomBudget() {
  //fill(168, 213, 128, interpolators[14].value*2);
  fill(35, 150, 215, interpolators[14].value);
  rect(0, 0, width, height);
  fill(185, interpolators[14].value);
  //fill(255, 100, 100, interpolators[14].value);
  noStroke();
  pushStyle();
  rectMode(CENTER);
  ellipse(95, height/2-50, 110, 110);
  textAlign(CENTER, CENTER);
  textSize(20);
  fill(255, interpolators[14].value);
  text("Voto ("+(10-count)+")", 95, height/2-50);
  popStyle();
  //rectMode(CORNERS);
  for (int i=0; i<11; i++) {
    float x1 = map(i, 0, 10, 10, width-60);
    float y1 = map(customValues[i], 100000, 180000000, height-100, 20);
    rect(x1, y1, x1+50, height-50);
    pushStyle();
    fill(255, 1.5*interpolators[14].value);
    rect(x1+6, height-70, x1+20, height-56);
    rect(x1+30, height-70, x1+44, height-56);
    stroke(100, 1.5*interpolators[14].value);
    strokeWeight(2);
    line(x1+8, height-63, x1+18, height-63);
    line(x1+32, height-63, x1+42, height-63);
    line(x1+37, height-68, x1+37, height-58);
    fill(255, interpolators[14].value);
    textAlign(CENTER, TOP);
	textSize(11);
    text(names[i], x1+25, height-45);
	text(customValues[i], x1+25, y1-15);
    popStyle();
  }
  float xa = map(pos, 0, 10, 10, width-60);
  fill(250, upordown[0].value);
  rect(xa+10, height-150, xa+40, height-100);
  triangle(xa, height-150, xa+25, height-175, xa+50, height-150);
  fill(120, upordown[1].value);
  rect(xa+10, height-150, xa+40, height-100);
  triangle(xa, height-100, xa+25, height-75, xa+50, height-100);
}
void updateVotes(int cathegory, int p) {
  votes[cathegory]+=p;
  count++;
  pos=cathegory;
  if(p==1) upordown[0].set(255);
  else if(p==-1) upordown[1].set(255);
  /*String[] line=loadStrings("http://localhost/updateData.php?col="+cathegory+"&value="+p);
  println(line[0]);*/
}
void sendFeedback(){
	String[] lines = loadStrings("http://tablo-al.com/updateData.php?c="+count+"&v0="+votes[0]+"&v1="+votes[1]+"&v2="+votes[2]+"&v3="+votes[3]+"&v4="+votes[4]+"&v5="+votes[5]+"&v6="+votes[6]+"&v7="+votes[7]+"&v8="+votes[8]+"&v9="+votes[9]+"&v10="+votes[10]);
}
boolean details() {
  for (int i=0; i<11; i++) {
    float y = map(interpolators[i].value, 100000, 180000000, 550, 20);
    if (mouseX>10 + i*(width-70)/10 && mouseX<60 + i*(width-70)/10) {
      if (mouseY>y && mouseY<(height-50)) {
        return true;
      }
    }
  }
  return false;
}
void intro() {
  if (colint[0].value>5) {
    pushStyle();
    background(168, 213, 128, colint[0].value);
    rectMode(CENTER);
    fill(173, 32, 60, colint[0].value);
    noStroke();
    ellipse(width/2, height/2-40, animinter[0].value, animinter[0].value);
    fill(64, 148, 178, colint[0].value);
    ellipse(width/2, height/2+120, animinter[1].value, animinter[1].value);
    textAlign(CENTER, CENTER);
    fill(0, colint[0].value);
	textSize(32);
	text("BUXHETI I HAPUR", width/2, 100);
	textSize(18);
    text("BUXHETET", width/2, height/2-40);
    text("INFO", width/2, height/2+120);
    popStyle();
    if (dist(mouseX, mouseY, width/2, height/2-40)<55) {
      b1=true;
    }
    else b1=false;
    if (dist(mouseX, mouseY, width/2, height/2+120)<45) {
      b2=true;
    }
    else b2=false;
  }
}
void information() {
  if (colint[1].value>5) {
    pushStyle();
    background(168, 213, 128, colint[1].value);
    fill(173, 32, 60, colint[1].value);
    rectMode(CENTER);
    noStroke();
    ellipse(width/2, height/2+140, animinter[2].value, animinter[2].value);
    textAlign(CENTER, CENTER);
    textSize(14);
    fill(0, colint[1].value);
    text("Buxheti i hapur është një aplikacion që bën vizualizimin e zërave të buxhetit", width/2, 100);
	text("të Republikës së Shqipërisë, sipas disa kategorive lehtësisht të interpretueshme ", width/2, 120);
	text("nga publiku i gjerë. Ai bën të mundur krahasimin e ecurisë së këtyre zërave", width/2, 140);
	text("ndër vite, raportin e tyre me zërat e tjerë, si dhe jep një përshtypje vizuale se ç'pjesë", width/2, 160);
	text("të buxhetit zë defiçiti.", width/2, 180);
	text("Gjithashtu, aplikacioni i jep mundësinë përdoruesit të shprehë mendimin e tij", width/2, 220);
	text("rreth shpërndarjes së buxhetit në zëra të ndryshëm. Çdo përdorues mund të japë", width/2, 240);
	text("deri në 10 vlerësime në muaj se cili zë mendon se duhet të financohet më shumë", width/2, 260);
	text("dhe cili financohet më shumë se ç'duhet. Rezultati do jetë një buxhet alternativ,", width/2, 280);
	text("dhe me demokratik i formuar prej përgjigjeve të përdoruesve", width/2, 300);
	textSize(18);
    text("BUXHETET", width/2, height/2+140);
    popStyle();
    if (dist(mouseX, mouseY, width/2, height/2+140)<55) {
      b3=true;
    }
    else b3=false;
  }
}
void showCharts(){
  if(colint[3].value>5){
    pushStyle();
    rectMode(CORNER);
    fill(168, 213, 128, colint[3].value);
	stroke(150, 200, 110, colint[3].value);
    rect(15, 40, 550, 300);
	if(dist(mouseX, mouseY, 530, 160)<15)
	fill(150, 150, 255, colint[3].value);
	else
	fill(100, 100, 255, colint[3].value);
	ellipse(530, 160, 30, 30);
	stroke(255, colint[3].value);
	line(520, 170, 528, 155);
	line(528, 155, 535, 165);
	line(535, 165, 542, 150);
	noStroke();
	if(dist(mouseX, mouseY, 530, 220)<15)
	fill(150, 150, 255, colint[3].value);
	else
	fill(100, 100, 255, colint[3].value);
	ellipse(530, 220, 30, 30);
	stroke(255, colint[3].value);
	line(522, 220, 538, 220);
	line(522, 215, 538, 215);
    line(522, 225, 538, 225);
	if(graph){
	float x3, y3;
	for(int i=0, n=budgets.size(); i<n; i++){
	  float y2 = map(cathegorised[index][i], min(cathegorised[index]), max(cathegorised[index]), 0, 100);
	  float x2 = map(i, 0, (n-1), 0, 400);
	  detailed[i].update(y2);
	  if(i>0){
	    x3 = map((i-1), 0, (n-1), 0, 400);
		y3 = map(cathegorised[index][i-1], min(cathegorised[index]), max(cathegorised[index]), 0, 100);
		detailed[i-1].update(y3);	
	  }
	  else{
	    x3=x2;
		y3=y2;
	  }	  
	  stroke(50, 150, 250, colint[3].value);
	  if(i == currentBudget)
	  strokeWeight(10);
	  else
	  strokeWeight(5);
	  point(50+x2, 220-detailed[i].value);
	  strokeWeight(1);
	  if(i>0)
	  line(50+x3, 220-detailed[i-1].value, 50+x2, 220-detailed[i].value);
	  textSize(12);
	  textAlign(CENTER);
	  fill(100, colint[3].value);
	  text("20"+(11+i), 50+x2, 320);
	}
    fill(50, colint[3].value);
	textAlign(LEFT);
	textSize(14);
	text(names2[index] +" - " + cathegorised[index][currentBudget], 80, 80);
	}
	else{
	    textSize(14);
		fill(50, colint[3].value);
		if(index!=10){
	    text(names2[index]+"  ("+(2011+currentBudget)+")", 80, 80);
		text("Pagat: "+budgets.get(currentBudget).values[index][0], 50, 110);
		text("Kontribute për sigurimet shoqërore: "+budgets.get(currentBudget).values[index][1], 50, 132);
		text("Mallra dhe shërbime: "+budgets.get(currentBudget).values[index][2], 50, 154);
		text("Subvencione: "+budgets.get(currentBudget).values[index][3], 50, 176);
		text("Të tjera transferta korrente të brendshme: "+budgets.get(currentBudget).values[index][4], 50, 198);
		text("Transferta korrent të huaja: "+budgets.get(currentBudget).values[index][5], 50, 220);
		text("Transferta për buxhetet familjare dhe individët: "+budgets.get(currentBudget).values[index][6], 50, 242);
		text("Shpenzime kapitale të patrupëzuara: "+budgets.get(currentBudget).values[index][7], 50, 264);
		text("Shpenzime kapitale të trupëzuara: "+budgets.get(currentBudget).values[index][8], 50, 286);
		text("Totali: "+budgets.get(currentBudget).values[index][9], 50, 308);
		}
		else{
		text(names2[index]+"  ("+(2011+currentBudget)+")", 80, 80);
		text("Në zërin 'Shpenzime të tjera të paklasifikuara' përfshihen: ", 50, 110);
		text("1. Pagesat për shërbimin e borxhit", 50, 140);
		text("2. Kontigjenca për politik pagash e pensionesh", 50, 170);
		text("3. Fondi rezerve", 50, 200);
		text("4. Shpenzimet e pushtetit vendor", 50, 230);
		text("5. Detyrimet e prapambetura", 50, 260);
		text("Totali: "+budgets.get(currentBudget).values[index][9], 50, 290);
		}
	}
    popStyle();
  }
}
//kete po e shtoj per prove
void giveMeTheId(String var123){
  fbId = var123;
  String[] lines3 = loadString("http://tablo-al.com/getId.php?id="+fbId);
  count = int(lines3[0]);
}

void check(String var456){
    fbId = var456;

}

class Budget {
  int[][] values;
  String year;
  int total;
  int deficit;
  int incomes;
  Budget(String s, int[][] v, int t, int d) {
    year = s;
    values = v;
    total = t;
    deficit = d;
    incomes = t-d;
  }
}
class Integrator{
  final float DAMPING=0.5f;
  final float ATTRACTION=0.2f;
  float value;
  float vel;
  float accel;
  float force;
  float mass=1;
  float damping=DAMPING;
  float attraction=ATTRACTION;
  boolean targeting;
  Integrator(){}
  
  Integrator(float v){
    value=v;
	targeting=false;
  }
  Integrator(float value, float damping, float attraction){
    this.value=value;
    this.damping=damping;
    this.attraction=attraction;
  }
  
  void set(float v){
    value=v;
  }
  
  void update(int t){
    float target=(float)t;
    force+=attraction*(target-value);
    accel=force/mass;
    vel=(vel+accel)*damping;
    value+=vel;
    force=0;
  } 
  void noTarget(){
    targeting=false;
  }
  void target(float t){
    targeting=true;
  }
}
</script>
</head>

<body bgcolor="#A8D580" style="overflow-x: hidden;">
<script>

  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    if (response.status === 'connected') {
      testAPI();
    } else if (response.status === 'not_authorized') {
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      document.getElementById('status').innerHTML = ' ';
    }
  }

  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '252389941613917',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.0' // use version 2.0
  });

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Faleminderit që u loguat, ' + response.name + '!';
      if(response.id != null){
          var pjs = Processing.getInstanceById("canvas1");
          pjs.giveMeTheId(response.id);
          pjs.check(response.id);
      }
    });

  }

</script>

<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>

<div id="status"></div>

<canvas id="canvas1"></canvas>
</body>
</html>

