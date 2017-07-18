<?php
try {
   $xmldas = SDO_DAS_XML::create("https://main.g2planet.com/codetest/example.xsd");
   $document = $xmldas->loadFile("https://main.g2planet.com/codetest/example.xml");
   $root_data_object = $document->getRootDataObject();
} catch (SDO_Exception $e) {
   print($e->getMessage());
}
?>