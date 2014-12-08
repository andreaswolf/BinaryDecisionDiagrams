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
	 *
	 */
	protected function sortAllowedInputs() {
		usort($this->allowedInputs, function ($a, $b) {
			$i = 1;$sort = 0;
			foreach (array_reverse($this->variables) as $variable) {
				if (isset($a[$variable]) && !isset($b[$variable])) {
					$sort -= pow(2, $i);
				}
				if (!isset($a[$variable]) && isset($b[$variable])) {
					$sort += pow(2, $i);
				}
				++$i;
			}
		});
	}

	/**
	 * @return array
	 */
	protected function getNodesForDiagram() {
		// the first two nodes are the possible end states 0 and 1
		$this->nodes = array();
		$lastVariable = $lastInsertedNode = NULL;

		//$this->sortAllowedInputs();

		$this->expandNodes();
		$this->nodes = array_merge(array(array(0), array(1)), $this->nodes);

		return $this->nodes;
	}

	protected function expandNodes() {
		$variable = array_shift($this->variables);
		list($falseInputs, $trueInputs) = $this->filterInputsByVariableValue($this->allowedInputs, $variable);
		$falseNode = $this->expandNodesForFirstVariable($this->variables, FALSE, $falseInputs);
		$trueNode = $this->expandNodesForFirstVariable($this->variables, TRUE, $trueInputs);

		$this->nodes[] = array($variable, $falseNode, $trueNode, $this->nodeCounter);
	}

	protected function expandNodesForFirstVariable($variableStack, $expansionValue, $inputs) {
		$variable = array_shift($variableStack);
echo "inputs: ", count($inputs), "\n";
		if (count($inputs) == 0) {
			// we’re at the bottom of the tree
			if ($expansionValue == FALSE) {
				$leftNode = 1;
				$rightNode = 0;
			} else {
				$leftNode = 0;
				$rightNode = 1;
			}
		} else {
			//echo "Expanding for $variable, ", $expansionValue ? 'true' : 'false', "\n";
			list($falseInputs, $trueInputs) = $this->filterInputsByVariableValue($inputs, $variable);
			//echo "Inputs: ", count($trueInputs), " ", count($falseInputs), "\n";

			$leftNode = $this->expandNodesForFirstVariable($variableStack, FALSE, $falseInputs);
			$rightNode = $this->expandNodesForFirstVariable($variableStack, TRUE, $trueInputs);
		}
		$this->nodes[] = array($variable, $leftNode, $rightNode, $this->nodeCounter);
		++$this->nodeCounter;

		return $this->nodeCounter - 1;
	}

	/**
	 * @param $inputs
	 * @param $variable
	 * @return array
	 */
	protected function filterInputsByVariableValue($inputs, $variable) {
		$trueInputs = $falseInputs = array();
		// filter out inputs where the current variable is set to FALSE
		foreach ($inputs as $input) {
			if (!isset($input[$variable])) {
				$falseInputs[] = $input;
				$trueInputs[] = $input;
				continue;
			}
			if ($input[$variable] == FALSE) {
				$falseInputs[] = $input;
			} else {
				$trueInputs[] = $input;
			}
		}

		return array($falseInputs, $trueInputs);
	}

	/**
	 * @param $nodes
	 * @param $variable
	 * @param $falseOutput
	 * @param $trueOutput
	 * @return array|NULL
	 */
	private function findNodeForVariable($nodes, $variable, $falseOutput, $trueOutput) {
echo "Lookup: $variable – $falseOutput – $trueOutput in this array:\n";
print_r($nodes);
		foreach ($nodes as $node) {
			if ($node[0] == $variable && ($falseOutput == -1 || $node[1] == $falseOutput) && ($trueOutput == -1 || $node[2] == $trueOutput)) {
				return $node;
			}
		}

		return NULL;
	}

}
