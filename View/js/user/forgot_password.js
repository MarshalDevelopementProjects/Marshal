const EmailSubmissionForm = document.getElementById("forgot-password-form");

EmailSubmissionForm.addEventListener('submit', async function(event) {
    event.preventDefault();

    try {
        let formData_1 = new FormData(EmailSubmissionForm);
        let formDataObj_1 = Object.fromEntries(formData_1);

        let response_1 = await fetch("http://localhost/public/user/forgot/password/verification", {
            mode: "cors",
            method: "POST",
            body: formData_1
        });
        let data_1 = await response_1.json();
        if (response_1.ok) {
            alert(data_1.message);

            const VerificationCodeSubmissionFormDiv = document.getElementById("verification-form-div");
            const VerificationCodeSubmissionForm = document.getElementById("verification-code-submission-form");

            VerificationCodeSubmissionFormDiv.setAttribute("style", "display: inline");
            VerificationCodeSubmissionForm.addEventListener("submit", async function(event) {
                event.preventDefault();

                let formData_2 = new FormData(VerificationCodeSubmissionForm);
                formData_2.append("email_address", formDataObj_1.email_address);
                let formDataObj_2 = Object.fromEntries(formData_2);

                let response_2 = await fetch("http://localhost/public/user/forgot/password/verify", {
                    withCredentials: true,
                    credentials: "include",
                    mode: "cors",
                    method: "POST",
                    body: formData_2
                });
                let data_2 = await response_2.json();
                if (response_2.ok) {
                    alert(data_2.message);

                    const PasswordUpdateFormDiv = document.getElementById("password-update-form-div");
                    const PasswordUpdateForm = document.getElementById("password-update-form");

                    PasswordUpdateFormDiv.setAttribute("style", "display: inline");
                    PasswordUpdateForm.addEventListener("submit", async function(event) {
                        event.preventDefault();

                        let formData_3 = new FormData(PasswordUpdateForm);
                        formData_3.append("email_address", formDataObj_1.email_address);
                        let formDataObj_3 = Object.fromEntries(formData_3);
                        console.log(formDataObj_3);

                        let response_3 = await fetch("http://localhost/public/user/forgot/password/update", {
                            withCredentials: true,
                            credentials: "include",
                            mode: "cors",
                            method: "PUT",
                            body: JSON.stringify(formDataObj_3)
                        });

                        let data_3 = await response_3.json();
                        if (response_3.ok) {
                            alert(data_3.message);
                            location.replace("http://localhost/public/user/login");
                        } else {
                            alert(data_3.message);
                            // location.reload();
                        }
                    });

                } else {
                    alert(data_2.message);
                    // location.reload();
                }
            });
        } else {
            alert(data_1.message);
            location.replace("http://localhost/public/user/login");
        }
    } catch (error) {
        console.error(error);
    }

});