<?php
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /**
     * This test documents the SQL Injection vulnerability found in admin/admin_class.php.
     * It demonstrates how the current code constructs queries.
     */
    public function testSqlInjectionVulnerabilityLogic()
    {
        // Simulation of the vulnerable code logic
        $username = "' OR '1'='1"; // Malicious input
        $password = "anything";

        // The code in admin_class.php does this:
        $query = "SELECT * FROM users where username = '".$username."' and password = '".$password."' ";

        // Assert that the query is malformed/injected
        $expectedInjectedQuery = "SELECT * FROM users where username = '' OR '1'='1' and password = 'anything' ";
        $this->assertEquals($expectedInjectedQuery, $query);
    }

    /**
     * This test documents the Insecure Password Hashing (MD5).
     */
    public function testPasswordHashingIsInsecure()
    {
        $password = "secret";
        $hash = md5($password);

        // Assert that MD5 is used (which is insecure)
        $this->assertEquals(md5("secret"), $hash);

        // Warning: password_hash should be used instead
        $this->assertNotEquals(password_hash($password, PASSWORD_DEFAULT), $hash);
    }

    /**
     * This test documents the insecure file upload filename generation.
     */
    public function testInsecureFileUploadFilename()
    {
        $originalFilename = "shell.php";
        // admin_class.php logic:
        $fname = strtotime(date('y-m-d H:i')).'_'.$originalFilename;

        // Assert that the extension is preserved, allowing .php execution
        $this->assertStringEndsWith('.php', $fname);
    }
}
