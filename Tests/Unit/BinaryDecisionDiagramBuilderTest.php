<?php
namespace AndreasWolf\BinaryDecisionDiagrams\Tests\Unit;


use AndreasWolf\BinaryDecisionDiagrams\BinaryDecisionDiagramBuilder;


class BinaryDecisionDiagramBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function diagramForOneVariablePassthroughFunctionIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A'));

		$subject->addAllowedInput(array('A' => FALSE), FALSE);
		$subject->addAllowedInput(array('A' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramForOneVariableInverterIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A'));

		$subject->addAllowedInput(array('A' => TRUE), FALSE);
		$subject->addAllowedInput(array('A' => FALSE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertTrue($diagram->getResultForInput(array('A' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramForBooleanAndOfTwoVariablesIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B'));

		$subject->addAllowedInput(array('A' => TRUE, 'B' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
	}

	/**
	 * @test
	 */
	public function diagramForLogicalXorOfTwoVariablesIsCorrectlyBuilt() {
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B'));

		$subject->addAllowedInput(array('A' => TRUE, 'B' => FALSE), TRUE);
		$subject->addAllowedInput(array('A' => FALSE, 'B' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
	}

	/**
	 * @test
	 */
	public function partiallyDefinedInputForFunctionIsAccepted() {
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B'));

		$subject->addAllowedInput(array('A' => TRUE), TRUE);
		$subject->addAllowedInput(array('A' => FALSE, 'B' => TRUE), TRUE);

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => FALSE, 'B' => TRUE)));
	}

	/**
	 * @test
	 */
	public function functionWithOrsNestedInAndIsCorrectlyBuilt() {
		// (A || B) && (C || D)
		$subject = new BinaryDecisionDiagramBuilder(array('A', 'B', 'C', 'D'));

		$subject->addAllowedInput(array('A' => FALSE, 'B' => TRUE, 'C' => FALSE, 'D' => TRUE), TRUE); // FTFT
		$subject->addAllowedInput(array('A' => FALSE, 'B' => TRUE, 'C' => TRUE), TRUE); // FTT-
		$subject->addAllowedInput(array('A' => TRUE, 'C' => FALSE, 'D' => TRUE), TRUE); // T-FT
		$subject->addAllowedInput(array('A' => TRUE, 'C' => TRUE), TRUE); // T-T-

		$diagram = $subject->getDiagram();

		$this->assertFalse($diagram->getResultForInput(array('A' => FALSE, 'B' => FALSE, 'C' => TRUE, 'D' => FALSE)));
		$this->assertFalse($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE, 'C' => FALSE, 'D' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE, 'C' => FALSE, 'D' => TRUE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => FALSE, 'C' => TRUE, 'D' => FALSE)));
		$this->assertTrue($diagram->getResultForInput(array('A' => TRUE, 'B' => TRUE, 'C' => TRUE, 'D' => TRUE)));
	}

}
