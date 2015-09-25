<?php
use LeanCloud\LeanClient;
use LeanCloud\LeanException;

/**
 * Testing API behaviors
 */

class LeanAPITest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        LeanClient::initialize(
            getenv("LC_APP_ID"),
            getenv("LC_APP_KEY"),
            getenv("LC_APP_MASTER_KEY"));
        LeanClient::useRegion("CN");
    }

    public function testIncrementOnStringField() {
        $obj = array("name" => "alice");
        $resp = LeanClient::post("/classes/TestObject", $obj);

        $this->setExpectedException("LeanCloud\LeanException",
                                    "111 Invalid value type for field", 111);
        $resp2 = LeanClient::put("/classes/TestObject/" . $resp["objectId"],
                                 array("name" => array("__op" => "Increment",
                                                       "amount" => 1)));
    }

    /**
     * Increment a field on new object is allowed.
     */
    public function testIncrementOnNewObject() {
        $obj = array("name" => "alice",
                     "score" => array("__op" => "Increment",
                                      "amount" => 1));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);
    }

    public function testAddOnNewObject() {
        $obj = array("name" => "alice",
                     "tags" => array("__op" => "Add",
                                     "objects" => array("frontend")));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);
    }

    public function testAddUniqueOnAddField() {
        $obj = array("name" => "alice",
                     "tags" => array("__op" => "Add",
                                     "objects" => array("frontend", "frontend")));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);

        $resp2 = LeanClient::get("/classes/TestObject/{$resp["objectId"]}");
        $this->assertEquals(array("frontend", "frontend"), $resp2["tags"]);

        $resp3 = LeanClient::put("/classes/TestObject/{$resp["objectId"]}",
                                 array("tags" => array("__op" => "AddUnique",
                                                       "objects" => array("css"))));
        // AddUnique will not remove exsiting duplicate items
        $resp4 = LeanClient::get("/classes/TestObject/{$resp["objectId"]}");
        $this->assertEquals(array("frontend", "frontend", "css"),
                            $resp4["tags"]);
    }

    public function testHeterogeneousObjectsInArray() {
        $obj = array("name" => "alice",
                     "tags" => array("foo", 42, array("a", "b")));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);
    }

    public function testSetHashValue() {
        $obj = array("name" => "alice",
                     "attr" => array("age" => 12,
                                     "gender" => "female"));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);

        // Add hash pair to hash field is not valid
        $this->setExpectedException("LeanCloud\LeanException", null, 1);
        $resp2 = LeanClient::put("/classes/TestObject/{$resp["objectId"]}",
                                 array("attr" => array(
                                     "__op" => "add",
                                     "objects" => array("favColor" => "Orange"))));
    }

    public function testAddRelation() {
        $obj = array("name" => "alice",
                     "likes" => array("__op" => "AddRelation",
                                      "objects" => array(
                                          array("__type" => "Pointer",
                                                "className" => "Post",
                                                "objectId" => "3a43bcbc3"))));
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);
    }

    /**
     * Batch on array operation will result error:
     *
     * 301 - Fails to insert new document, cannot update on ...
     *       at the same time.
     */
    public function testBatchOperationOnArray() {
        $obj = array("name" => "Batch test", "tags" => array());
        $resp = LeanClient::post("/classes/TestObject", $obj);
        $this->assertNotEmpty($resp["objectId"]);


        $adds    = array("__op" => "Add",
                         "objects" => array("javascript", "frontend"));
        $removes = array("__op" => "Remove",
                         "objects" => array("frontend", "css"));
        $obj     = array("tags" => array("__op" => "Batch",
                                         "ops"  => array($adds, $removes)));

        $this->setExpectedException("LeanCloud\LeanException", null, 301);
        $resp = LeanClient::put("/classes/TestObject/{$resp['objectId']}",
                                $obj);
    }

}
?>