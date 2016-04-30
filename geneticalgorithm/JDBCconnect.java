package geneticalgorithm;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class JDBCconnect{
   // JDBC driver name and database URL
    static Map <String,String> chromosome=new HashMap<String,String>();
   JDBCconnect(String name,int hr,int rr,int bp_s,int bp_d,int ecg,int age){
       
	  List <Integer> parameters=new ArrayList<Integer>();
   
        /* int hr  = rs.getInt("hr");
         int bp_s = rs.getInt("bp_s");
         int bp_d=rs.getInt("bp_d");
         int rr=rs.getInt("rr");
         int ecg=rs.getInt("ecg");
         int age=rs.getInt("age");
         name = rs.getString("name"); */
	  
         parameters.add(hr);
         parameters.add(rr);
         parameters.add(bp_s);
         parameters.add(bp_d);
         parameters.add(ecg);
         parameters.add(age);
       
         Patient p=new Patient(name,parameters);
         chromosome=p.geneRepresentation();
         
         System.out.println("\n"+"Chromosome Formed from data :"+chromosome+"\n");
         for(String x : chromosome.keySet())      
         	new GA(x,chromosome.get(x).toString()).run();
       
         
   }//end constructor
}//end JDBCExample