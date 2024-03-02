class University {
    String uname;
    String ucity;
    int tot_stud;

    public University(String uname, String ucity, int tot_stud) {
        this.uname = uname;
        this.ucity = ucity;
        this.tot_stud = tot_stud;
    }
}

class Department extends University {
    int deptno;
    String deptname;

    public Department(String uname, String ucity, int tot_stud, int deptno, String deptname) {
        super(uname, ucity, tot_stud);
        this.deptno = deptno;
        this.deptname = deptname;
    }

    public void display() {
        System.out.println("University Name: " + uname);
        System.out.println("City: " + ucity);
        System.out.println("Total Students: " + tot_stud);
        System.out.println("Department Number: " + deptno);
        System.out.println("Department Name: " + deptname);
    }
}

public class demo {
    public static void main(String[] args) {
        // Creating two objects and demonstrating
        Department university1 = new Department("ABC University", "City1", 5000, 101, "Computer Science");
        Department university2 = new Department("XYZ University", "City2", 7000, 201, "Electrical Engineering");

        // Displaying information for both objects
        System.out.println("Information for University 1:");
        university1.display();
        System.out.println("\nInformation for University 2:");
        university2.display();
    }
}
