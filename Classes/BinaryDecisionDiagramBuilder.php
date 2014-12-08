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
		$inputItems = $this->allowedInputs;

		$variable = $this->variables[0];
		$trueNodes = $this->filterInputsForVariableValue($variable, TRUE, $this->allowedInputs);
		$falseNodes = $this->filterInputsForVariableValue($variable, FALSE, $this->allowedInputs);
		if (count($trueNodes)) {
			$this->nodes[] = array($variable, 0, 1);
		}
		if (count($falseNodes)) {
			$this->nodes[] = array($variable, 1, 0);
		}
	}

	protected function filterInputsForVariableValue($variable, $value, $inputs) {
		$filtered = array();

		foreach ($inputs as $input) {
			if ($input[$variable] == $value) {
				$filtered[] = $input;
			}
		}
		return $filtered;
	}


}
