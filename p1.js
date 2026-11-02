let selectedSection = "";
let selectedType = "";

function selectSection(section) {
    selectedSection = section;

    document.getElementById("typeBox").classList.remove("hidden");
    document.getElementById("resultBox").classList.add("hidden");

    document.getElementById("sectionTitle").innerText =
        section + " Section";
}

function selectType(type) {
    selectedType = type;

    let url = "";
    if (selectedSection === "BCA" || selectedSection === "BCS") {
        if (type === "Class") {
            url = "class_and_lab_availability/bca_class.php";  
        } else if (type === "Lab") {
            url = "class_and_lab_availability/bca_lab.php";  }
    } else if (selectedSection === "BTECH") {
        if (type === "Class") {
            url = "class_and_lab_availability/btech_class.php";  
        } else if (type === "Lab") {
            url = "class_and_lab_availability/btech_lab.php"; 
        }
    }

    if (url) {
        window.location.href = url;  
    } 
    else {
        document.getElementById("resultBox").classList.remove("hidden");
        document.getElementById("resultText").innerText =
            "Invalid selection. Please try again.";
    }
}