<?php
namespace AndreasWolf\BinaryDecisionDiagrams;

/**
 * A simple builder for binary decision diagrams.
 *
 * Accepts a set of variables and input combinations on these variables and resolves them to a binary decision diagram.
 * The input function needs to be fully (i.e. all possible input combinations have to be covered) and unambiguously
 * (no two outputs might exist for the same input) defined.
 *
 * @author Andreas Wolf <aw@foundata.net>
 */
class BinaryDecisionDiagramBuilder {

	/**
	 * @var string[]
	 */
	protected $variables;

	/**
	 * @var array
	 */
	protected $allowedInputs;

	/**
	 * @var int
	 */
	protected $nodeCounter = 2;

	/**
	 * @var array
	 */
	protected $nodes = array();

	/**
	 * @param string[] $variables
	 */
	public function __construct(array $variables) {
		$this->variables = $variables;
	}

	/**
	 * @param array $inputValues The input values as a hash map (variable => input). Values that are not set will be
	 * treated as a wildcard
	 * @param $output
	 */
	public function addAllowedInput(array $inputValues, $output) {
		if ($output !== TRUE) {
			// we only store the minterms, i.e. those terms that result in TRUE
			return;
		}
		$this->allowedInputs[] = $inputValues;
	}

	/**
	 * @return BinaryDecisionDiagram
	 */
	public function getDiagram() {
		return new BinaryDecisionDiagram($this->variables, $this->getNodesForDiagram());
	}

	/**
	 * Computes and returns the nodes from the diagram.
	 *
	 * @return array
	 */
	protected function getNodesForDiagram() {
		// the first two nodes are the possible end states 0 and 1
		$this->nodes = array();
		$lastVariable = $lastInsertedNode = NULL;

		$this->developForVariable(0, $this->allowedInputs);
		$this->nodes = array_merge(array(array(0), array(1)), $this->nodes);

		return $this->nodes;
	}

	/**
	 * @param int $variableNumber
	 * @param array $inputs
	 * @return int The uid of a
	 */
	protected function developForVariable($variableNumber, $inputs) {
		if ($variableNumber >= count($this->variables)) {
			return (count($inputs) > 0) ? 1 : 0;
		}
		$variable = $this->variables[$variableNumber];
		$filteredForTrue = $this->filterInputsForVariableValue($this->variables[$variableNumber], TRUE, $inputs);
		$filteredForFalse = $this->filterInputsForVariableValue($this->variables[$variableNumber], FALSE, $inputs);

		$trueNode = $this->developForVariable($variableNumber + 1, $filteredForTrue);
		$falseNode = $this->developForVariable($variableNumber + 1, $filteredForFalse);
		if ($trueNode == $falseNode) {
			return $trueNode;
		}

		$this->nodes[] = array($variable, $falseNode, $trueNode, $this->nodeCounter);
		++$this->nodeCounter;

		return $this->nodeCounter - 1;
	}

	/**
	 * Filters the given inputs by the given variable value.
	 *
	 * "Don’t care" values (= variable value not set) are always included.
	 *
	 * @param string $variableName
	 * @param boolean $value The value of the variable
	 * @param array $inputs The inputs to filter
	 * @return array
	 */
	protected function filterInputsForVariableValue($variableName, $value, $inputs) {
		$filtered = array();

		foreach ($inputs as $input) {
			if (!isset($input[$variableName]) || (isset($input[$variableName]) && $input[$variableName] == $value)) {
				$filtered[] = $input;
			}
		}
		return $filtered;
	}

}
