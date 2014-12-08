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
		$nodes = array();
		$nodeCounter = 2;
		$lastVariable = $lastInsertedNode = NULL;

		$this->sortAllowedInputs();

		foreach ($this->allowedInputs as $index => $input) {
			echo ">> Building for input $index\n";
			// we have only stored minterms, so as long as we don’t encounter an input, everything should default to
			// FALSE
			$lastFalseNode = 0;
			$lastTrueNode = 1;

			// climb from the bottom to the top
			foreach (array_reverse($this->variables) as $variable) {
				echo ">> Building for variable $variable\n";
				if (!isset($input[$variable])) {
					echo ">> variable not found\n";
					// variable is not set, i.e. this input does not care for it
					continue;
				}
				$candidateNode = $this->findNodeForVariable($nodes, $variable, $lastFalseNode, $lastTrueNode);

				if ($candidateNode == NULL) {
					echo ">> No node found\n";
					echo ">> inserting node: $variable/$lastFalseNode/$lastTrueNode/$nodeCounter\n";
					$nodes[] = array($variable, $lastFalseNode, $lastTrueNode, $nodeCounter);
					if ($input[$variable] === TRUE) {
						$lastTrueNode = $nodeCounter;
					} elseif ($input[$variable] === FALSE) {
						$lastFalseNode = $nodeCounter;
					}
					$lastInsertedNode = $nodes[count($nodes)-1];
					echo ">> last FALSE/TRUE node: $lastFalseNode/$lastTrueNode\n";
					++$nodeCounter;
				} else {
					echo ">> last FALSE/TRUE node: $lastFalseNode/$lastTrueNode\n";
					print_r($candidateNode);
					if ($input[$variable] === TRUE) {
						$lastTrueNode = $candidateNode[3];
					} elseif ($input[$variable] === FALSE) {
						$lastFalseNode = $candidateNode[3];
					}
					$lastInsertedNode = NULL;
					print_r($nodes);
				}
			}
			$lastVariable = $variable;
		}
		$nodes = array_merge(array(array(), array()), $nodes);

		return $nodes;
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
			if ($node[0] == $variable && $node[1] == $falseOutput && $node[2] == $trueOutput) {
				return $node;
			}
		}

		return NULL;
	}

}
