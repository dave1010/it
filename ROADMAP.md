# ROADMAP

Inline method support now instantiates non-static methods automatically and allows overriding the subject under test via the
optional `with` callable (leveraging PHP 8.5's flexible static closures). Next improvements to consider:

- Allow passing constructor arguments declaratively so that simple value injection does not require a custom `with` closure.
- Provide lifecycle hooks to reuse an object across multiple inline tests on the same class when appropriate.
- Expand reporting with richer context (class name, visibility, static/non-static) to aid debugging when multiple inline
  tests exist on a single method.
