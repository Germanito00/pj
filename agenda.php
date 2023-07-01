<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Agenda Telefônica</title>
  <style>
    /* Estilos ... (mesmo código CSS do exemplo anterior) */
  </style>
</head>
<body>
  <?php
    // Configuração do banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "contacts";

    try {
      // Conexão com o banco de dados usando PDO
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // Configura o PDO para lançar exceções em caso de erros
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Função para exibir os contatos
      function displayContacts($conn) {
        $sql = "SELECT * FROM contacts";
        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
          echo "<table id='contactTable'>";
          echo "<thead>";
          echo "<tr>";
          echo "<th>Nome</th>";
          echo "<th>E-mail</th>";
          echo "<th>Telefone</th>";
          echo "<th>Ações</th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>";
            echo "<button class='button' onclick='editContact(this, " . $row["id"] . ")'><img src='edit-icon.png' alt='Edit' class='button-icon'>Editar</button>";
            echo "<button class='button' onclick='deleteContact(" . $row["id"] . ")'><img src='delete-icon.png' alt='Delete' class='button-icon'>Excluir</button>";
            echo "</td>";
            echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";
        } else {
          echo "Nenhum contato encontrado.";
        }
      }

      // Verifica se o formulário foi enviado
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];

        // Insere um novo contato no banco de dados
        $sql = "INSERT INTO contacts (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        if ($stmt->execute()) {
          echo "Contato salvo com sucesso.";
        } else {
          echo "Erro ao salvar o contato.";
        }
      }

      // Exclui um contato do banco de dados
      if (isset($_GET["delete"])) {
        $id = $_GET["delete"];

        $sql = "DELETE FROM contacts WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
          echo "Contato excluído com sucesso.";
        } else {
          echo "Erro ao excluir o contato.";
        }
      }

      // Exibe os contatos
      displayContacts($conn);

      // Fecha a conexão com o banco de dados
      $conn = null;
    } catch (PDOException $e) {
      echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
  ?>

  <div class="phone-container">
    <div class="phone-header">Agenda Telefônica</div>
    <div class="phone-content">
      <form id="contactForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="phone">Telefone:</label>
        <input type="text" id="phone" name="phone" required><br>
        <button type="submit" class="button"><img src="save-icon.png" alt="Save" class="button-icon">Salvar</button>
      </form>
    </div>
  </div>

  <script>
    // Função para editar um contato
    function editContact(button, id) {
      var row = button.parentNode.parentNode;
      var name = row.cells[0].innerHTML;
      var email = row.cells[1].innerHTML;
      var phone = row.cells[2].innerHTML;

      document.getElementById('name').value = name;
      document.getElementById('email').value = email;
      document.getElementById('phone').value = phone;

      row.parentNode.removeChild(row);

      // Adiciona um campo oculto com o ID do contato a ser editado
      var hiddenIdField = document.createElement("input");
      hiddenIdField.type = "hidden";
      hiddenIdField.name = "id";
      hiddenIdField.value = id;
      document.getElementById('contactForm').appendChild(hiddenIdField);
    }

    // Função para excluir um contato
    function deleteContact(id) {
      if (confirm("Tem certeza que deseja excluir este contato?")) {
        window.location.href = "agenda.php?delete=" + id;
      }
    }
  </script>
</body>
</html>
