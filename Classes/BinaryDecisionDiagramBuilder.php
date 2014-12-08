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
	 * @return array
	 */
	protected function getNodesForDiagram() {
		// the first two nodes are the possible end states 0 and 1
		$nodes = array(array(), array());
		foreach ($this->allowedInputs as $input) {
			// we have only stored minterms, so as long as we don’t encounter an input, everything should default to
			// FALSE
			$lastFalseOutput = 0;
			$lastTrueOutput = 0;

			// climb from the bottom to the top
			foreach (array_reverse($this->variables) as $variable) {
				if (!isset($input[$variable])) {
					// variable is not set, i.e. this input does not care for it
					continue;
				}

				if ($input[$variable] === TRUE) {
					$lastTrueOutput = count($nodes) - 1;
				} elseif ($input[$variable] === FALSE) {
					$lastFalseOutput = count($nodes) - 1;
				}
				$nodes[] = array($variable, $lastFalseOutput, $lastTrueOutput);
			}
		}

		return $nodes;
	}

}
