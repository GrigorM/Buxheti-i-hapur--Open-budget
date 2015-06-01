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
