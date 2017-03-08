<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

/**
 * Provides step definitions for debugging step definitions.
 */
class DebugContext extends RawWordpressContext
{
    /**
     * Pause the scenario until the user presses a key.
     *
     * Example: Then I add a breakpint
     *
     * @Then I add a breakpoint
     */
    public function iAddABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");

        while (fgets(STDIN, 1024) === '') {
        }

        fwrite(STDOUT, "\033[u");
    }
}
