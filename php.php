<?php

class Persoana {
    protected $nume;
    protected $prenume;
    protected $idnp;
    protected $dataNasterii;

    public function __construct($nume, $prenume, $idnp, $dataNasterii) {
        $this->nume = $nume;
        $this->prenume = $prenume;
        $this->idnp = $idnp;
        $this->dataNasterii = $dataNasterii;
    }

    public function toString() {
        return "{$this->nume} {$this->prenume}, IDNP: {$this->idnp}, Data nașterii: {$this->dataNasterii}";
    }
}

class Student extends Persoana {
    public $medii;
    private $grupa;
    private $absenteMotivate;
    private $absenteNemotivate;
    private $bursa;

    public function __construct($nume, $prenume, $idnp, $dataNasterii, $medii, $grupa, $absenteMotivate, $absenteNemotivate) {
        parent::__construct($nume, $prenume, $idnp, $dataNasterii);
        $this->medii = $medii; // un array de 8 note
        $this->grupa = $grupa;
        $this->absenteMotivate = $absenteMotivate;
        $this->absenteNemotivate = $absenteNemotivate;
        $this->bursa = 0;
    }

    public function toString() {
        return parent::toString() . ", Grupă: {$this->grupa}, Absențe motivate: {$this->absenteMotivate}, Absențe nemotivate: {$this->absenteNemotivate}, Media generală: {$this->media()}, Bursa: {$this->getBursa()}" ;
    }

    public function media() {
        return array_sum($this->medii) / count($this->medii);
    }

    public function getBursa() {
        return $this->bursa;
    }

    public function setBursa($bursa) {
        $this->bursa = $bursa;
    }

    public function restanta(){
        foreach ($this->medii as $medie){
            if ($medie < 5) return true ;
        }
    }
    public function norestanta(){
        foreach ($this->medii as $medie){
            if ($medie < 5) return true ;
        }
    }
}

//a) Funcția de citire a studenților din fișier într-un tablou;
function citireStudentiDinFisier($numeFisier) {
    $studenti = [];
    $linii = file($numeFisier, FILE_IGNORE_NEW_LINES);
    foreach ($linii as $linie) {
        $date = explode(',', $linie);
        $medii = array_map('floatval', explode('|', $date[4])); 
        $studenti[] = new Student($date[0], $date[1], $date[2], $date[3], $medii, $date[5], intval($date[6]), intval($date[7]));
    }
    return $studenti;
}
//b) Funcția de afișare a studenților în fereastra browserului;
function afisareStudenti($studenti) {
    echo "Studenti<br>";
    foreach ($studenti as $student) {
        echo $student->toString() . "<br>";
    }
}
//c) Funcția de afișare a tuturor studenților, care au restanțe;
function afisareStudentiRestantieri($studenti) {
    echo "Studenti restantieri<br>";
    foreach ($studenti as $student) {
        if($student->restanta()) echo $student->toString() . "<br>";
    }
}
//e) Funcția de calcul a bursei
function calculBursa($studenti){
    
    $studentiFaraRestante = array_filter($studenti, function($student) {
        return !$student->restanta(); 
    });
    
    usort($studentiFaraRestante, function($a, $b) {
        return $b->media() <=> $a->media(); 
    });
    
    foreach ($studentiFaraRestante as $index => $student) {
        if ($index == 0) {
            $student->setBursa(1000); 
        } elseif ($index == 1) {
            $student->setBursa(900); 
        } elseif ($index >= 2 && $index <= 7) {
            $student->setBursa(800); 
        } else {
            $student->setBursa(0); 
        }
    }

    return $studenti;
}





//Formularul de adăugare (alipire) a unui student în fișier;
function adaugareStudent(){
    $nume = trim($_POST['nume']);
    $prenume = trim($_POST['prenume']);
    $idnp = trim($_POST['idnp']);
    $data_nasterii = trim($_POST['data_nasterii']);
    $medii = trim($_POST['medii']);
    $grupa = trim($_POST['grupa']);
    $absente_motivate = intval($_POST['absente_motivate']);
    $absente_nemotivate = intval($_POST['absente_nemotivate']);
    $data_de_adaugat = "$nume,$prenume,$idnp,$data_nasterii,$medii,$grupa,$absente_motivate,$absente_nemotivate\n";
    file_put_contents('studenti.in', $data_de_adaugat, FILE_APPEND);
}
//adaugareStudent();
$studenti = citireStudentiDinFisier('studenti.in');
usort($studenti, function($a, $b) {
    return $b->media() <=> $a->media(); 
});
calculBursa($studenti);
afisareStudenti($studenti);
afisareStudentiRestantieri(($studenti));
?>

